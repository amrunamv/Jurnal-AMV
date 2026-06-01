<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_COMPLETED = 'completed';

    const RECOMMENDATION_ACCEPT = 'accept';
    const RECOMMENDATION_MINOR_REVISION = 'minor_revision';
    const RECOMMENDATION_MAJOR_REVISION = 'major_revision';
    const RECOMMENDATION_REJECT = 'reject';

    protected $fillable = [
        'manuscript_id',
        'revision_id',
        'reviewer_id',
        'assigned_by',
        'status',
        'comments_to_author',
        'comments_to_editor',
        'scores',
        'recommendation',
        'round',
        'assigned_at',
        'due_date',
        'accepted_at',
        'completed_at',
    ];

    protected $casts = [
        'scores' => 'array',
        'round' => 'integer',
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function revision(): BelongsTo
    {
        return $this->belongsTo(ManuscriptRevision::class, 'revision_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== self::STATUS_COMPLETED;
    }
}
