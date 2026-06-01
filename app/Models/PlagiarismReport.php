<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlagiarismReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'revision_id',
        'similarity_score',
        'provider',
        'report_url',
        'certificate_file',
        'details',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'similarity_score' => 'decimal:2',
        'details' => 'array',
        'checked_at' => 'datetime',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(ManuscriptRevision::class, 'revision_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function isPassing(): bool
    {
        // Generally, similarity below 25% is acceptable
        return $this->similarity_score !== null && $this->similarity_score <= 25;
    }
}
