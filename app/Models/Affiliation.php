<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ror_id',
        'country',
        'city',
        'website_url',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function contributors(): HasMany
    {
        return $this->hasMany(Contributor::class);
    }
}
