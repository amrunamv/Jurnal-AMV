<?php

namespace App\Services;

use App\Models\Citation;
use App\Models\Manuscript;

class ReferenceParserService
{
    /**
     * Parse raw citation text into structured data
     */
    public function parse(string $rawText): array
    {
        $rawText = trim($rawText);
        
        // Try to detect DOI first
        $doi = $this->extractDoi($rawText);
        
        // Try to detect URL
        $url = $this->extractUrl($rawText);
        
        // Extract year
        $year = $this->extractYear($rawText);
        
        // Try to parse authors
        $authors = $this->extractAuthors($rawText);
        
        // Try to extract title (text in quotes or before journal name)
        $title = $this->extractTitle($rawText);
        
        // Try to extract journal info
        $journalInfo = $this->extractJournalInfo($rawText);
        
        return [
            'raw_text' => $rawText,
            'authors' => $authors,
            'title' => $title,
            'journal' => $journalInfo['journal'] ?? null,
            'volume' => $journalInfo['volume'] ?? null,
            'issue' => $journalInfo['issue'] ?? null,
            'pages' => $journalInfo['pages'] ?? null,
            'year' => $year,
            'doi' => $doi,
            'url' => $url,
            'type' => $this->detectType($rawText),
            'is_parsed' => !empty($title) || !empty($doi),
        ];
    }

    /**
     * Parse multiple references
     */
    public function parseMultiple(array $references): array
    {
        return array_map(fn($ref) => $this->parse($ref), $references);
    }

    /**
     * Save parsed citations to manuscript
     */
    public function saveToManuscript(Manuscript $manuscript, array $rawReferences): array
    {
        $citations = [];
        
        foreach ($rawReferences as $index => $rawText) {
            $parsed = $this->parse($rawText);
            
            $citation = Citation::updateOrCreate(
                [
                    'manuscript_id' => $manuscript->id,
                    'order' => $index + 1,
                ],
                $parsed
            );
            
            $citations[] = $citation;
        }
        
        return $citations;
    }

    /**
     * Extract DOI from text
     */
    protected function extractDoi(string $text): ?string
    {
        // DOI patterns
        $patterns = [
            '/doi:\s*(10\.\d{4,}\/[^\s]+)/i',
            '/https?:\/\/doi\.org\/(10\.\d{4,}\/[^\s]+)/i',
            '/(10\.\d{4,}\/[^\s\]\)]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return rtrim($matches[1], '.,;');
            }
        }

        return null;
    }

    /**
     * Extract URL from text
     */
    protected function extractUrl(string $text): ?string
    {
        if (preg_match('/https?:\/\/[^\s\]\)]+/', $text, $matches)) {
            return rtrim($matches[0], '.,;');
        }
        
        return null;
    }

    /**
     * Extract year from text
     */
    protected function extractYear(string $text): ?int
    {
        // Look for years in parentheses first (common in APA)
        if (preg_match('/\((\d{4})\)/', $text, $matches)) {
            $year = (int) $matches[1];
            if ($year >= 1800 && $year <= 2100) {
                return $year;
            }
        }
        
        // Look for standalone year
        if (preg_match('/\b(19\d{2}|20\d{2})\b/', $text, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Extract authors from text
     */
    protected function extractAuthors(string $text): array
    {
        $authors = [];
        
        // Try to find author section (before year in parentheses)
        if (preg_match('/^(.+?)\s*\(\d{4}\)/', $text, $matches)) {
            $authorSection = $matches[1];
            $authors = $this->parseAuthorSection($authorSection);
        }
        
        return $authors;
    }

    /**
     * Parse author section into structured array
     */
    protected function parseAuthorSection(string $section): array
    {
        $authors = [];
        
        // Split by common separators
        $parts = preg_split('/,\s*&\s*|,\s*and\s*|&|\band\b/i', $section);
        
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;
            
            // Try to split name into family and given
            if (preg_match('/^([^,]+),\s*(.+)$/', $part, $matches)) {
                $authors[] = [
                    'family' => trim($matches[1]),
                    'given' => trim($matches[2]),
                ];
            } else {
                // Assume last word is family name
                $words = explode(' ', $part);
                $family = array_pop($words);
                $given = implode(' ', $words);
                
                if ($family) {
                    $authors[] = [
                        'family' => $family,
                        'given' => $given ?: null,
                    ];
                }
            }
        }
        
        return $authors;
    }

    /**
     * Extract title from text
     */
    protected function extractTitle(string $text): ?string
    {
        // Look for text in quotes
        if (preg_match('/"([^"]+)"/', $text, $matches)) {
            return $matches[1];
        }
        
        // Try to extract title after year (APA style)
        if (preg_match('/\(\d{4}\)\.\s*([^.]+)\./', $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extract journal info (volume, issue, pages)
     */
    protected function extractJournalInfo(string $text): array
    {
        $info = [];
        
        // Volume and issue: e.g., "Volume 5, Issue 2" or "5(2)"
        if (preg_match('/(\d+)\s*\((\d+)\)/', $text, $matches)) {
            $info['volume'] = $matches[1];
            $info['issue'] = $matches[2];
        } elseif (preg_match('/Vol(?:ume)?\.?\s*(\d+)/i', $text, $matches)) {
            $info['volume'] = $matches[1];
        }
        
        // Pages: e.g., "pp. 10-20" or "10-20" or "p. 10"
        if (preg_match('/pp?\.?\s*(\d+[-–]\d+|\d+)/i', $text, $matches)) {
            $info['pages'] = str_replace('–', '-', $matches[1]);
        } elseif (preg_match('/:\s*(\d+[-–]\d+)/', $text, $matches)) {
            $info['pages'] = str_replace('–', '-', $matches[1]);
        }
        
        // Try to extract journal name (italic text or after title)
        if (preg_match('/\.\s*([A-Z][^,.]+(?:Journal|Review|Letters|Science|Research)[^,.]*)/i', $text, $matches)) {
            $info['journal'] = trim($matches[1]);
        }
        
        return $info;
    }

    /**
     * Detect reference type
     */
    protected function detectType(string $text): string
    {
        $text = strtolower($text);
        
        if (str_contains($text, 'conference') || str_contains($text, 'proceedings')) {
            return 'conference';
        }
        
        if (str_contains($text, 'book') || str_contains($text, 'publisher') || str_contains($text, 'press')) {
            return 'book';
        }
        
        if (str_contains($text, 'thesis') || str_contains($text, 'dissertation')) {
            return 'thesis';
        }
        
        if (str_contains($text, 'http') || str_contains($text, 'www.')) {
            return 'website';
        }
        
        return 'journal';
    }
}
