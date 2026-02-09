<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Person extends Model
{
  use HasSlug;

  protected $fillable = [
      'name',
      'slug',
      'stage_name',
      'full_name',
      'birth_date',
      'death_date',
      'gender',
      'sexuality',
      'birth_country',
      'birth_city',
      'picture',
      'is_published',
      'is_hidden',
  ];

  public function casts(): array
  {
    return [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_published' => 'boolean',
        'is_hidden' => 'boolean',
    ];
  }

  public function pictureUrl(?string $extension = null): ?string
  {
    if (! $this->picture) {
      return null;
    }

    if ($extension === null || trim($extension) === '') {
      return Storage::url($this->picture);
    }

    $normalizedExtension = strtolower(ltrim(trim($extension), '.'));

    if (! in_array($normalizedExtension, ['gif', 'jpg', 'webp', 'avif'], true)) {
      return Storage::url($this->picture);
    }

    $directory = pathinfo($this->picture, PATHINFO_DIRNAME);
    $filename = pathinfo($this->picture, PATHINFO_FILENAME);

    $path = $directory === '.'
      ? "{$filename}.{$normalizedExtension}"
      : "{$directory}/{$filename}.{$normalizedExtension}";

    return Storage::url($path);
  }
}
