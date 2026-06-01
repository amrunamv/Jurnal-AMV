<?php

namespace App\Services;

use App\Models\Manuscript;
use App\Models\CrossrefLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CrossrefService
{
    protected string $apiUrl = 'https://api.crossref.org/v2/deposits';
    protected string $testApiUrl = 'https://test.crossref.org/v2/deposits';
    protected string $username;
    protected string $password;
    protected string $doiPrefix;
    protected bool $testMode;

    public function __construct()
    {
        $this->username = \App\Models\Setting::get('crossref_username', config('services.crossref.username', ''));
        $this->password = \App\Models\Setting::get('crossref_password', config('services.crossref.password', ''));
        $this->doiPrefix = \App\Models\Setting::get('crossref_doi_prefix', config('services.crossref.doi_prefix', '10.00000'));
        $this->testMode = (bool) \App\Models\Setting::get('crossref_test_mode', config('services.crossref.test_mode', true));
    }

    /**
     * Generate a DOI for a manuscript
     */
    public function generateDoi(Manuscript $manuscript): string
    {
        // Format: prefix/journal.year.volume.issue.articleid
        $journalCode = Str::slug($manuscript->journal->slug, '');
        $year = $manuscript->published_at?->year ?? now()->year;
        
        return "{$this->doiPrefix}/{$journalCode}.{$year}.{$manuscript->id}";
    }

    /**
     * Register DOI with Crossref
     */
    public function registerDoi(Manuscript $manuscript): CrossrefLog
    {
        $doi = $manuscript->doi ?? $this->generateDoi($manuscript);
        $batchId = Str::uuid()->toString();
        
        // Build XML deposit
        $xml = $this->buildDepositXml($manuscript, $doi, $batchId);

        // Create log entry
        $log = CrossrefLog::create([
            'manuscript_id' => $manuscript->id,
            'batch_id' => $batchId,
            'doi' => $doi,
            'status' => CrossrefLog::STATUS_PENDING,
            'request_payload' => ['xml' => $xml],
        ]);

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders(['Content-Type' => 'application/xml'])
                ->post($this->getApiUrl(), $xml);

            if ($response->successful()) {
                $log->update([
                    'status' => CrossrefLog::STATUS_SUBMITTED,
                    'submitted_at' => now(),
                    'response_payload' => $response->json() ?? ['body' => $response->body()],
                ]);

                // Update manuscript DOI
                $manuscript->update(['doi' => $doi]);
            } else {
                $log->update([
                    'status' => CrossrefLog::STATUS_FAILED,
                    'error_message' => $response->body(),
                    'response_payload' => $response->json() ?? ['body' => $response->body()],
                ]);
            }
        } catch (\Exception $e) {
            $log->update([
                'status' => CrossrefLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Check DOI registration status
     */
    public function checkStatus(CrossrefLog $log): CrossrefLog
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->get($this->getApiUrl() . '/' . $log->batch_id);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'completed') {
                    $log->update([
                        'status' => CrossrefLog::STATUS_REGISTERED,
                        'registered_at' => now(),
                        'response_payload' => $data,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't change status
        }

        return $log->fresh();
    }

    /**
     * Build Crossref deposit XML
     */
    protected function buildDepositXml(Manuscript $manuscript, string $doi, string $batchId): string
    {
        $timestamp = now()->format('YmdHis');
        $title = htmlspecialchars($manuscript->title, ENT_XML1);
        $abstract = htmlspecialchars($manuscript->abstract, ENT_XML1);
        $journalTitle = htmlspecialchars($manuscript->journal->name, ENT_XML1);
        $issn = $manuscript->journal->e_issn ?? $manuscript->journal->issn ?? '';
        $publicationDate = $manuscript->published_at ?? now();
        $year = $publicationDate->format('Y');
        $month = $publicationDate->format('m');
        $day = $publicationDate->format('d');
        $articleUrl = config('app.url') . '/articles/' . $manuscript->slug;

        // Build contributors
        $contributors = '';
        foreach ($manuscript->contributors as $index => $contributor) {
            $sequence = $index === 0 ? 'first' : 'additional';
            $role = $contributor->is_corresponding ? 'author' : 'author';
            $givenName = htmlspecialchars($contributor->given_name, ENT_XML1);
            $surname = htmlspecialchars($contributor->family_name, ENT_XML1);
            
            $orcid = '';
            if ($contributor->orcid) {
                $orcid = "<ORCID>https://orcid.org/{$contributor->orcid}</ORCID>";
            }

            $contributors .= <<<XML
            <person_name sequence="{$sequence}" contributor_role="{$role}">
                <given_name>{$givenName}</given_name>
                <surname>{$surname}</surname>
                {$orcid}
            </person_name>
            XML;
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doi_batch version="5.3.1" xmlns="http://www.crossref.org/schema/5.3.1"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.crossref.org/schema/5.3.1 http://www.crossref.org/schemas/crossref5.3.1.xsd">
    <head>
        <doi_batch_id>{$batchId}</doi_batch_id>
        <timestamp>{$timestamp}</timestamp>
        <depositor>
            <depositor_name>AMV Open Science</depositor_name>
            <email_address>{$this->username}</email_address>
        </depositor>
        <registrant>AMV Open Science</registrant>
    </head>
    <body>
        <journal>
            <journal_metadata language="id">
                <full_title>{$journalTitle}</full_title>
                <issn media_type="electronic">{$issn}</issn>
            </journal_metadata>
            <journal_article publication_type="full_text">
                <titles>
                    <title>{$title}</title>
                </titles>
                <contributors>
                    {$contributors}
                </contributors>
                <jats:abstract xmlns:jats="http://www.ncbi.nlm.nih.gov/JATS1">
                    <jats:p>{$abstract}</jats:p>
                </jats:abstract>
                <publication_date media_type="online">
                    <month>{$month}</month>
                    <day>{$day}</day>
                    <year>{$year}</year>
                </publication_date>
                <doi_data>
                    <doi>{$doi}</doi>
                    <resource>{$articleUrl}</resource>
                </doi_data>
            </journal_article>
        </journal>
    </body>
</doi_batch>
XML;
    }

    /**
     * Get API URL based on mode
     */
    protected function getApiUrl(): string
    {
        return $this->testMode ? $this->testApiUrl : $this->apiUrl;
    }
}
