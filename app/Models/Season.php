<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
  use HasFactory, HasSlug;

  protected $fillable = [
      'show_id',
      'name',
      'slug',
      'overview',
      'season_number',
      'episode_count',
      'air_date',
      'vote_average',
      'poster_path',
      'tmdb_id',
      'is_published',
      'is_hidden',
  ];

  public function casts(): array
  {
    return [
        'air_date' => 'date',
        'is_published' => 'boolean',
        'is_hidden' => 'boolean',
    ];
  }

  public function show(): BelongsTo
  {
    return $this->belongsTo(Show::class);
  }

  public function episodes(): HasMany
  {
    return $this->hasMany(Episode::class);
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

  /**
   * Scope slug uniqueness to the parent show.
   */
  protected static function slugQuery(string $column, ?int $ignoreId): Builder
  {
    return parent::slugQuery($column, $ignoreId);
  }

  /**
   * Create a unique slug scoped to a show.
   */
  public static function createScopedSlug(string $value, int $showId, ?int $ignoreId = null): string
  {
    $slug = \Illuminate\Support\Str::slug($value);

    $exists = static::query()
        ->where('show_id', $showId)
        ->where('slug', $slug)
        ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
        ->exists();

    if (! $exists) {
      return $slug;
    }

    $max = static::query()
        ->where('show_id', $showId)
        ->where('slug', 'like', $slug.'-%')
        ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
        ->pluck('slug')
        ->filter(fn (string $existing) => preg_match('/^'.preg_quote($slug, '/').'(-\d+)$/', $existing))
        ->map(fn (string $existing) => static::extractSuffix($existing))
        ->max() ?? 0;

    return $slug.'-'.($max + 1);
  }
}
