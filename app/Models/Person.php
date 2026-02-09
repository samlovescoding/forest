<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

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

  public function pictureUrl($extension = null, $default = null)
  {
    if (! isset($this->picture)) {
      return $default;
    }
    if (isset($extension)) {
      $pictureWithExtension = str($this->picture)->beforeLast('.')->append('.', $extension);

      return asset('storage/'.$pictureWithExtension);
    }

    return asset('storage/'.$this->picture);
  }
}
