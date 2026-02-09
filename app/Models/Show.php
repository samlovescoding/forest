<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Show extends Model
{
  use HasSlug;

  protected $fillable = [
      'name',
      'slug',
      'overview',
      'episode_run_time',
      'number_of_seasons',
      'number_of_episodes',
      'first_air_date',
      'last_air_date',
      'vote_count',
      'vote_average',
      'popularity',
      'backdrop_path',
      'poster_path',
      'tmdb_id',
      'imdb_id',
      'is_published',
      'is_hidden',
  ];

  public function casts()
  {
    return [
        'is_published' => 'boolean',
        'is_hidden' => 'boolean',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
    ];
  }

  public function genres(): BelongsToMany
  {
    return $this->belongsToMany(Genre::class);
  }
}
