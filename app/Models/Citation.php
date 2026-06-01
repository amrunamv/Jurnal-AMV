<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Citation extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'order',
        'raw_text',
        'authors',
        'title',
        'journal',
        'volume',
        'issue',
        'pages',
        'year',
        'doi',
        'url',
        'publisher',
        'type',
        'is_parsed',
    ];

    protected $casts = [
        'authors' => 'array',
        'year' => 'integer',
        'is_parsed' => 'boolean',
        'order' => 'integer',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    // Format citation in different styles
    public function formatApa(): string
    {
        $authors = $this->formatAuthorsApa();
        $year = $this->year ? "({$this->year})" : '';
        $title = $this->title ?? '';
        $journal = $this->journal ? "<i>{$this->journal}</i>" : '';
        $volume = $this->volume ? "<i>{$this->volume}</i>" : '';
        $issue = $this->issue ? "({$this->issue})" : '';
        $pages = $this->pages ?? '';
        $doi = $this->doi ? "https://doi.org/{$this->doi}" : '';

        return trim("{$authors} {$year}. {$title}. {$journal}, {$volume}{$issue}, {$pages}. {$doi}");
    }

    private function formatAuthorsApa(): string
    {
        if (empty($this->authors)) {
            return '';
        }

        $formatted = [];
        foreach ($this->authors as $author) {
            $familyName = $author['family'] ?? '';
            $givenName = $author['given'] ?? '';
            $initials = $givenName ? strtoupper(substr($givenName, 0, 1)) . '.' : '';
            $formatted[] = "{$familyName}, {$initials}";
        }

        if (count($formatted) === 1) {
            return $formatted[0];
        }

        $last = array_pop($formatted);
        return implode(', ', $formatted) . ', & ' . $last;
    }
}
