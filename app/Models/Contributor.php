<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'user_id',
        'given_name',
        'family_name',
        'email',
        'orcid',
        'affiliation_id',
        'affiliation_text',
        'role',
        'order',
        'is_corresponding',
    ];

    protected $casts = [
        'is_corresponding' => 'boolean',
        'order' => 'integer',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(Affiliation::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->given_name} {$this->family_name}");
    }

    public function getAffiliationNameAttribute(): string
    {
        return $this->affiliation?->name ?? $this->affiliation_text ?? '';
    }
}
