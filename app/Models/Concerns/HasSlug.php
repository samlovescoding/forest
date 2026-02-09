<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasSlug
{
  /**
   * Create a unique slug for the given value.
   */
  public static function createSlug(string $value, string $column = 'slug', ?int $ignoreId = null): string
  {
    $slug = Str::slug($value);

    if (! static::slugExists($slug, $column, $ignoreId)) {
      return $slug;
    }

    return static::resolveUniqueSlug($slug, $column, $ignoreId);
  }

  /**
   * Determine if a slug already exists in the given column.
   */
  protected static function slugExists(string $slug, string $column, ?int $ignoreId): bool
  {
    return static::slugQuery($column, $ignoreId)
        ->where($column, $slug)
        ->exists();
  }

  /**
   * Resolve the next available unique slug.
   */
  protected static function resolveUniqueSlug(string $slug, string $column, ?int $ignoreId): string
  {
    $max = static::highestSlugSuffix($slug, $column, $ignoreId);

    return $slug.'-'.($max + 1);
  }

  /**
   * Get the highest numeric suffix for the given slug.
   */
  protected static function highestSlugSuffix(string $slug, string $column, ?int $ignoreId): int
  {
    return static::slugQuery($column, $ignoreId)
        ->where($column, 'like', $slug.'-%')
        ->pluck($column)
        ->filter(fn (string $existing) => preg_match('/^'.preg_quote($slug, '/').'(-\d+)$/', $existing))
        ->map(fn (string $existing) => static::extractSuffix($existing))
        ->max() ?? 0;
  }

  /**
   * Extract the numeric suffix from a slug.
   */
  protected static function extractSuffix(string $slug): int
  {
    return preg_match('/-(\d+)$/', $slug, $matches) ? (int) $matches[1] : 0;
  }

  /**
   * Get a base query builder scoped for slug lookups.
   */
  protected static function slugQuery(string $column, ?int $ignoreId): Builder
  {
    return static::query()
        ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId));
  }

  public function getRouteKeyName(): string
  {
    return 'slug';
  }
}
