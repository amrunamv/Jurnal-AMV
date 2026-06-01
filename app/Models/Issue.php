<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'volume_id',
        'issue_number',
        'title',
        'publication_date',
        'description',
        'cover_image',
        'is_published',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    public function manuscripts(): HasMany
    {
        return $this->hasMany(Manuscript::class);
    }

    public function journal(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Journal::class, Volume::class, 'id', 'id', 'volume_id', 'journal_id');
    }

    public function getNumberAttribute()
    {
        return $this->issue_number;
    }

    public function getPublishedAtAttribute()
    {
        return $this->publication_date;
    }
}
