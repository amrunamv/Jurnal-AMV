<?php

namespace App\Http\Controllers\Api;

use App\Models\Manuscript;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * OAI-PMH 2.0 Protocol Implementation
 * Supports: Identify, ListMetadataFormats, ListSets, ListRecords, GetRecord, ListIdentifiers
 */
class OaiPmhController extends Controller
{
    protected string $repositoryName = 'AMV Open Science';
    protected string $baseUrl;
    protected string $adminEmail = 'hallo@amvsd.id';

    public function __construct()
    {
        $this->baseUrl = url('/oai');
    }

    /**
     * Main OAI-PMH endpoint
     */
    public function handle(Request $request): Response
    {
        $verb = $request->input('verb');

        $response = match ($verb) {
            'Identify' => $this->identify(),
            'ListMetadataFormats' => $this->listMetadataFormats($request),
            'ListSets' => $this->listSets(),
            'ListRecords' => $this->listRecords($request),
            'ListIdentifiers' => $this->listIdentifiers($request),
            'GetRecord' => $this->getRecord($request),
            default => $this->error('badVerb', 'Illegal OAI verb'),
        };

        return response($response, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Identify - Repository information
     */
    protected function identify(): string
    {
        $earliestDate = Manuscript::published()
            ->orderBy('published_at', 'asc')
            ->value('published_at') ?? now();

        return $this->xmlResponse('Identify', '', <<<XML
<repositoryName>{$this->repositoryName}</repositoryName>
<baseURL>{$this->baseUrl}</baseURL>
<protocolVersion>2.0</protocolVersion>
<adminEmail>{$this->adminEmail}</adminEmail>
<earliestDatestamp>{$earliestDate->format('Y-m-d')}</earliestDatestamp>
<deletedRecord>transient</deletedRecord>
<granularity>YYYY-MM-DD</granularity>
XML);
    }

    /**
     * ListMetadataFormats - Supported metadata formats
     */
    protected function listMetadataFormats(Request $request): string
    {
        $identifier = $request->input('identifier');
        
        if ($identifier && !$this->recordExists($identifier)) {
            return $this->error('idDoesNotExist', 'The identifier does not exist');
        }

        return $this->xmlResponse('ListMetadataFormats', '', <<<XML
<metadataFormat>
    <metadataPrefix>oai_dc</metadataPrefix>
    <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
    <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
</metadataFormat>
XML);
    }

    /**
     * ListSets - Available sets (journals)
     */
    protected function listSets(): string
    {
        $journals = Journal::where('is_active', true)->get();
        \Illuminate\Support\Facades\Log::info('OAI ListSets called. Journals found: ' . $journals->count());
        
        $sets = '';
        foreach ($journals as $journal) {
            $sets .= "<set><setSpec>{$journal->slug}</setSpec><setName>{$this->escapeXml($journal->name)}</setName></set>\n";
        }

        return $this->xmlResponse('ListSets', '', $sets);
    }

    /**
     * ListRecords - List records with metadata
     */
    protected function listRecords(Request $request): string
    {
        $metadataPrefix = $request->input('metadataPrefix');
        
        if (!$metadataPrefix) {
            return $this->error('badArgument', 'Missing required argument: metadataPrefix');
        }

        if ($metadataPrefix !== 'oai_dc') {
            return $this->error('cannotDisseminateFormat', 'Unsupported metadata format');
        }

        $manuscripts = $this->getManuscripts($request);

        if ($manuscripts->isEmpty()) {
            return $this->error('noRecordsMatch', 'No records match the request');
        }

        $records = '';
        foreach ($manuscripts as $manuscript) {
            $records .= $this->buildRecord($manuscript);
        }

        return $this->xmlResponse('ListRecords', '', $records);
    }

    /**
     * ListIdentifiers - List record identifiers only
     */
    protected function listIdentifiers(Request $request): string
    {
        $metadataPrefix = $request->input('metadataPrefix');
        
        if (!$metadataPrefix) {
            return $this->error('badArgument', 'Missing required argument: metadataPrefix');
        }

        $manuscripts = $this->getManuscripts($request);

        if ($manuscripts->isEmpty()) {
            return $this->error('noRecordsMatch', 'No records match the request');
        }

        $headers = '';
        foreach ($manuscripts as $manuscript) {
            $headers .= $this->buildHeader($manuscript);
        }

        return $this->xmlResponse('ListIdentifiers', '', $headers);
    }

    /**
     * GetRecord - Get a single record
     */
    protected function getRecord(Request $request): string
    {
        $identifier = $request->input('identifier');
        $metadataPrefix = $request->input('metadataPrefix');

        if (!$identifier || !$metadataPrefix) {
            return $this->error('badArgument', 'Missing required arguments');
        }

        if ($metadataPrefix !== 'oai_dc') {
            return $this->error('cannotDisseminateFormat', 'Unsupported metadata format');
        }

        $uuid = str_replace('oai:amvopenscience.id:', '', $identifier);
        $manuscript = Manuscript::where('uuid', $uuid)->published()->first();

        if (!$manuscript) {
            return $this->error('idDoesNotExist', 'The identifier does not exist');
        }

        return $this->xmlResponse('GetRecord', '', $this->buildRecord($manuscript));
    }

    /**
     * Build OAI-PMH record with Dublin Core metadata
     */
    protected function buildRecord(Manuscript $manuscript): string
    {
        $header = $this->buildHeader($manuscript);
        $metadata = $this->buildDublinCore($manuscript);

        return <<<XML
<record>
    {$header}
    <metadata>
        {$metadata}
    </metadata>
</record>
XML;
    }

    /**
     * Build record header
     */
    protected function buildHeader(Manuscript $manuscript): string
    {
        $identifier = 'oai:amvopenscience.id:' . $manuscript->uuid;
        $datestamp = $manuscript->published_at->format('Y-m-d');
        $setSpec = $manuscript->journal->slug;

        return <<<XML
<header>
    <identifier>{$identifier}</identifier>
    <datestamp>{$datestamp}</datestamp>
    <setSpec>{$setSpec}</setSpec>
</header>
XML;
    }

    /**
     * Build Dublin Core metadata
     */
    protected function buildDublinCore(Manuscript $manuscript): string
    {
        $title = $this->escapeXml($manuscript->title);
        $abstract = $this->escapeXml($manuscript->abstract);
        $publisher = $this->escapeXml($manuscript->journal->publisher ?? $this->repositoryName);
        $date = $manuscript->published_at->format('Y-m-d');
        $identifier = $manuscript->doi 
            ? "https://doi.org/{$manuscript->doi}" 
            : config('app.url') . '/articles/' . $manuscript->slug;

        // Build authors
        $creators = '';
        foreach ($manuscript->contributors as $contributor) {
            $name = $this->escapeXml($contributor->full_name);
            $creators .= "<dc:creator>{$name}</dc:creator>\n";
        }

        // Build keywords
        $subjects = '';
        if ($manuscript->keywords) {
            foreach ($manuscript->keywords as $keyword) {
                $keyword = $this->escapeXml($keyword);
                $subjects .= "<dc:subject>{$keyword}</dc:subject>\n";
            }
        }

        // Build source (Journal Name & ISSN)
        $source = $this->escapeXml($manuscript->journal->name);
        if ($manuscript->journal->issn) {
            $source .= " (ISSN: {$manuscript->journal->issn})";
        } elseif ($manuscript->journal->e_issn) {
            $source .= " (e-ISSN: {$manuscript->journal->e_issn})";
        }

        // Build relation (Vol, Issue, Pages)
        $relation = '';
        if ($manuscript->issue) {
            $vol = $manuscript->issue->volume?->number ?? '';
            $no = $manuscript->issue->number ?? '';
            $relation = "Vol {$vol}, No {$no}";
            if ($manuscript->year) {
                 $relation .= " ({$manuscript->year})";
            }
        }
        
        // Build coverage (Publication Year)
        $coverage = $manuscript->published_at->year;

        return <<<XML
<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
           xmlns:dc="http://purl.org/dc/elements/1.1/"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
    <dc:title>{$title}</dc:title>
    {$creators}
    {$subjects}
    <dc:description>{$abstract}</dc:description>
    <dc:publisher>{$publisher}</dc:publisher>
    <dc:contributor>{$publisher}</dc:contributor>
    <dc:date>{$date}</dc:date>
    <dc:type>Text</dc:type>
    <dc:format>application/pdf</dc:format>
    <dc:identifier>{$identifier}</dc:identifier>
    <dc:source>{$source}</dc:source>
    <dc:language>id</dc:language>
    <dc:relation>{$relation}</dc:relation>
    <dc:coverage>{$coverage}</dc:coverage>
    <dc:rights>https://creativecommons.org/licenses/by/4.0/</dc:rights>
</oai_dc:dc>
XML;
    }

    /**
     * Get manuscripts based on request filters
     */
    protected function getManuscripts(Request $request)
    {
        $query = Manuscript::published()->with(['journal', 'contributors']);

        // Filter by set (journal)
        if ($set = $request->input('set')) {
            $journal = Journal::where('slug', $set)->first();
            if ($journal) {
                $query->where('journal_id', $journal->id);
            }
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->whereDate('published_at', '>=', $from);
        }

        if ($until = $request->input('until')) {
            $query->whereDate('published_at', '<=', $until);
        }

        return $query->orderBy('published_at', 'desc')->take(100)->get();
    }

    /**
     * Check if record exists
     */
    protected function recordExists(string $identifier): bool
    {
        $uuid = str_replace('oai:amvopenscience.id:', '', $identifier);
        return Manuscript::where('uuid', $uuid)->published()->exists();
    }

    /**
     * Build XML response wrapper
     */
    protected function xmlResponse(string $verb, string $attributes, string $content): string
    {
        $responseDate = now()->format('Y-m-d\TH:i:s\Z');

    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{$responseDate}</responseDate>
    <request verb="{$verb}" {$attributes}>{$this->baseUrl}</request>
    <{$verb}>
{$content}
    </{$verb}>
</OAI-PMH>
XML;
    }

    /**
     * Build error response
     */
    protected function error(string $code, string $message): string
    {
        $responseDate = now()->format('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{$responseDate}</responseDate>
    <request>{$this->baseUrl}</request>
    <error code="{$code}">{$message}</error>
</OAI-PMH>
XML;
    }

    /**
     * Escape XML special characters
     */
    protected function escapeXml($string): string
    {
        return htmlspecialchars((string) $string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
