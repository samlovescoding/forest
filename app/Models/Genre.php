<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    protected $fillable = [
        'name',
        'tmdb_id',
    ];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }

    public function shows(): BelongsToMany
    {
        return $this->belongsToMany(Show::class);
    }
}
