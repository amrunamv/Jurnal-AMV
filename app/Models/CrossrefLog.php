<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrossrefLog extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REGISTERED = 'registered';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'manuscript_id',
        'batch_id',
        'doi',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
        'submitted_at',
        'registered_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'submitted_at' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }
}
