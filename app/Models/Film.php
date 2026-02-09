<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
  use HasSlug;

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
