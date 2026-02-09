<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
  use HasFactory, HasSlug;

  protected $fillable = [
      'season_id',
      'show_id',
      'name',
      'slug',
      'overview',
      'episode_number',
      'season_number',
      'runtime',
      'air_date',
      'production_code',
      'vote_count',
      'vote_average',
      'still_path',
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

  public function season(): BelongsTo
  {
    return $this->belongsTo(Season::class);
  }

  public function show(): BelongsTo
  {
    return $this->belongsTo(Show::class);
  }

  public function stillUrl(?string $extension = null, mixed $default = null): mixed
  {
    if (! $this->still_path) {
      return $default;
    }

    $path = $extension
        ? str($this->still_path)->beforeLast('.')->append('.', $extension)
        : $this->still_path;

    return asset('storage/'.$path);
  }

  /**
   * Create a unique slug scoped to a season.
   */
  public static function createScopedSlug(string $value, int $seasonId, ?int $ignoreId = null): string
  {
    $slug = \Illuminate\Support\Str::slug($value);

    $exists = static::query()
        ->where('season_id', $seasonId)
        ->where('slug', $slug)
        ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
        ->exists();

    if (! $exists) {
      return $slug;
    }

    $max = static::query()
        ->where('season_id', $seasonId)
        ->where('slug', 'like', $slug.'-%')
        ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
        ->pluck('slug')
        ->filter(fn (string $existing) => preg_match('/^'.preg_quote($slug, '/').'(-\d+)$/', $existing))
        ->map(fn (string $existing) => static::extractSuffix($existing))
        ->max() ?? 0;

    return $slug.'-'.($max + 1);
  }
}
