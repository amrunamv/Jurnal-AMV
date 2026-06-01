<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuscriptActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper to log manuscript activities
    public static function log(
        Manuscript $manuscript,
        string $action,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $description = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'manuscript_id' => $manuscript->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
