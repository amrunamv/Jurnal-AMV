<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Manuscript extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;


    // Status constants for State Machine
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_EDITOR_REVIEW = 'editor_review';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVISION_REQUIRED = 'revision_required';
    const STATUS_REVISION_SUBMITTED = 'revision_submitted';
    const STATUS_COPYEDITING = 'copyediting';
    const STATUS_PRODUCTION = 'production';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'abstract',
        'keywords',
        'status',
        'journal_id',
        'issue_id',
        'submitter_id',
        'current_editor_id',
        'manuscript_file',
        'doi',
        'metadata',
        'page_start',
        'page_end',
        'view_count',
        'download_count',
        'submitted_at',
        'accepted_at',
        'published_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($manuscript) {
            if (empty($manuscript->uuid)) {
                $manuscript->uuid = Str::uuid();
            }
            if (empty($manuscript->slug)) {
                $manuscript->slug = Str::slug($manuscript->title) . '-' . Str::random(8);
            }
        });
    }

    // Relationships
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_editor_id');
    }

    public function contributors(): HasMany
    {
        return $this->hasMany(Contributor::class)->orderBy('order');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ManuscriptRevision::class)->orderByDesc('version');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function citations(): HasMany
    {
        return $this->hasMany(Citation::class)->orderBy('order');
    }

    public function plagiarismReports(): HasMany
    {
        return $this->hasMany(PlagiarismReport::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ManuscriptActivity::class)->orderByDesc('created_at');
    }

    public function crossrefLogs(): HasMany
    {
        return $this->hasMany(CrossrefLog::class);
    }

    // State Machine helpers
    public function canTransitionTo(string $status): bool
    {
        $transitions = [
            self::STATUS_DRAFT => [self::STATUS_SUBMITTED],
            self::STATUS_SUBMITTED => [self::STATUS_EDITOR_REVIEW, self::STATUS_REJECTED, self::STATUS_WITHDRAWN],
            self::STATUS_EDITOR_REVIEW => [self::STATUS_UNDER_REVIEW, self::STATUS_REJECTED, self::STATUS_WITHDRAWN],
            self::STATUS_UNDER_REVIEW => [self::STATUS_REVISION_REQUIRED, self::STATUS_COPYEDITING, self::STATUS_REJECTED],
            self::STATUS_REVISION_REQUIRED => [self::STATUS_REVISION_SUBMITTED, self::STATUS_WITHDRAWN],
            self::STATUS_REVISION_SUBMITTED => [self::STATUS_UNDER_REVIEW, self::STATUS_COPYEDITING, self::STATUS_REJECTED],
            self::STATUS_COPYEDITING => [self::STATUS_PRODUCTION],
            self::STATUS_PRODUCTION => [self::STATUS_PUBLISHED],
            self::STATUS_PUBLISHED => [],
            self::STATUS_REJECTED => [],
            self::STATUS_WITHDRAWN => [],
        ];

        return in_array($status, $transitions[$this->status] ?? []);
    }

    public function transitionTo(string $status): bool
    {
        if (!$this->canTransitionTo($status)) {
            return false;
        }

        $this->status = $status;
        return $this->save();
    }

    // Accessors
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getStatusLabelAttribute(): string
    {
        return __("common.statuses.{$this->status}") ?? $this->status;
    }

    public function getCorrespondingAuthorAttribute()
    {
        return $this->contributors()->where('is_corresponding', true)->first();
    }

    public function getLatestRevisionAttribute()
    {
        return $this->revisions()->first();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeByJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    /**
     * Define media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('manuscript_pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}

