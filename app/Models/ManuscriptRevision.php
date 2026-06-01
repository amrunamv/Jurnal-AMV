<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuscriptRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'manuscript_id',
        'version',
        'title_snapshot',
        'abstract_snapshot',
        'keywords_snapshot',
        'file_path',
        'author_notes',
        'editor_notes',
        'status',
        'uploaded_by',
    ];

    protected $casts = [
        'keywords_snapshot' => 'array',
        'version' => 'integer',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'revision_id');
    }
}
