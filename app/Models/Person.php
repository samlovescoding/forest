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
  ];

  public function casts()
  {
    return [
      'birth_date' => 'date',
      'death_date' => 'date',
    ];
  }
}
