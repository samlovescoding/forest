<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
  protected $fillable = [
      'title',
      'slug',
      'overview',
      'runtime',
      'release_date',
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
        'release_date' => 'date',
        'is_published' => 'boolean',
        'is_hidden' => 'boolean',
    ];
  }
}
