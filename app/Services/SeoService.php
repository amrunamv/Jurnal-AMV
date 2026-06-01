<?php

namespace App\Services;

use App\Models\Manuscript;
use Illuminate\Support\Collection;

class SeoService
{
    /**
     * Generate Highwire Press meta tags for a manuscript
     */
    public function generateHighwirePressTags(Manuscript $manuscript): array
    {
        $tags = [];

        $tags[] = ['name' => 'citation_title', 'content' => $manuscript->title];
        
        foreach ($manuscript->contributors as $contributor) {
            $tags[] = ['name' => 'citation_author', 'content' => $contributor->full_name];
            if ($contributor->affiliation) {
                $tags[] = ['name' => 'citation_author_institution', 'content' => $contributor->affiliation->name];
            }
        }

        if ($manuscript->published_at) {
            $tags[] = ['name' => 'citation_publication_date', 'content' => $manuscript->published_at->format('Y/m/d')];
            $tags[] = ['name' => 'citation_online_date', 'content' => $manuscript->published_at->format('Y/m/d')];
        }

        if ($manuscript->journal) {
            $tags[] = ['name' => 'citation_journal_title', 'content' => $manuscript->journal->name];
            if ($manuscript->journal->issn) {
                $tags[] = ['name' => 'citation_issn', 'content' => $manuscript->journal->issn];
            }
        }

        if ($manuscript->volume) {
            $tags[] = ['name' => 'citation_volume', 'content' => $manuscript->volume->number];
        }

        if ($manuscript->issue) {
            $tags[] = ['name' => 'citation_issue', 'content' => $manuscript->issue->number];
        }

        if ($manuscript->doi) {
            $tags[] = ['name' => 'citation_doi', 'content' => $manuscript->doi];
        }

        if ($manuscript->pdf_file) {
            $tags[] = ['name' => 'citation_pdf_url', 'content' => \Illuminate\Support\Facades\Storage::url($manuscript->pdf_file)];
        } else {
             $tags[] = ['name' => 'citation_pdf_url', 'content' => route('articles.download', $manuscript->slug)];
        }
        $tags[] = ['name' => 'citation_abstract_html_url', 'content' => config('app.url') . '/articles/' . $manuscript->slug];
        $tags[] = ['name' => 'citation_language', 'content' => 'en'];

        if ($manuscript->keywords) {
            foreach ($manuscript->keywords as $keyword) {
                $tags[] = ['name' => 'citation_keywords', 'content' => trim($keyword)];
            }
        }

        if ($manuscript->page_start) {
            $tags[] = ['name' => 'citation_firstpage', 'content' => $manuscript->page_start];
        }

        if ($manuscript->page_end) {
            $tags[] = ['name' => 'citation_lastpage', 'content' => $manuscript->page_end];
        }

        return $tags;
    }

    /**
     * Generate JSON-LD schema for an article
     */
    public function generateArticleSchema(Manuscript $manuscript): array
    {
        $authors = $manuscript->contributors->map(function ($contributor) {
            $data = [
                '@type' => 'Person',
                'name' => $contributor->full_name,
            ];
            if ($contributor->affiliation) {
                $data['affiliation'] = [
                    '@type' => 'Organization',
                    'name' => $contributor->affiliation->name,
                ];
            }
            return $data;
        })->toArray();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $manuscript->title,
            'description' => substr($manuscript->abstract, 0, 300),
            'author' => $authors,
            'datePublished' => $manuscript->published_at?->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => config('app.url') . '/logo.png',
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => config('app.url') . '/articles/' . $manuscript->slug,
            ],
            'identifier' => $manuscript->doi ? "doi:{$manuscript->doi}" : null,
        ];
    }

    /**
     * Get all SEO markup for the head section
     */
    public function getHeadMarkup(Manuscript $manuscript): string
    {
        $tags = $this->generateHighwirePressTags($manuscript);
        $schema = $this->generateArticleSchema($manuscript);

        $html = "<!-- Highwire Press Meta Tags -->\n";
        foreach ($tags as $tag) {
            $html .= "<meta name=\"" . $this->escapeXml($tag['name']) . "\" content=\"" . $this->escapeXml($tag['content']) . "\">\n";
        }

        $html .= "\n<!-- Schema.org JSON-LD -->\n";
        $html .= "<script type=\"application/ld+json\">\n";
        $html .= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        $html .= "</script>";

        return $html;
    }

    /**
     * Escape XML special characters
     */
    protected function escapeXml($string): string
    {
        return htmlspecialchars((string) $string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
