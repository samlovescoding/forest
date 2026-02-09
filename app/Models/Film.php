<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

  public function genres(): BelongsToMany
  {
    return $this->belongsToMany(Genre::class);
  }

  public function posterUrl(?string $extension = null, mixed $default = null): mixed
  {
    if (! $this->poster_path) {
      return $default;
    }

    $path = $extension
        ? str($this->poster_path)->beforeLast('.')->append('.', $extension)
        : $this->poster_path;

    return asset('storage/'.$path);
  }

  public function backdropUrl(?string $extension = null, mixed $default = null): mixed
  {
    if (! $this->backdrop_path) {
      return $default;
    }

    $path = $extension
        ? str($this->backdrop_path)->beforeLast('.')->append('.', $extension)
        : $this->backdrop_path;

    return asset('storage/'.$path);
  }
}
