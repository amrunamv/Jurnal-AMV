<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'editor', 'reviewer', 'author', 'reader']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'orcid',
        'scopus_id',
        'affiliation_id',
        'country_code',
        'bio',
        'bio_statement',
        'phone',
        'avatar',
        'research_interests',
        'is_reviewer_candidate',
        'h_index_scopus',
        'h_index_google_scholar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'research_interests' => 'array',
        ];
    }

    public function getFullNameAttribute(): string
    {
        if ($this->first_name) {
            return trim("{$this->salutation} {$this->first_name} {$this->middle_name} {$this->last_name}");
        }
        return $this->name;
    }

    // Relationships
    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(Affiliation::class);
    }

    public function submittedManuscripts(): HasMany
    {
        return $this->hasMany(Manuscript::class, 'submitter_id');
    }

    public function editingManuscripts(): HasMany
    {
        return $this->hasMany(Manuscript::class, 'current_editor_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function contributorProfiles(): HasMany
    {
        return $this->hasMany(Contributor::class);
    }

    /**
     * Scope a query to only include users with author role.
     */
    public function scopeAuthors($query)
    {
        return $query->role('author');
    }
}
