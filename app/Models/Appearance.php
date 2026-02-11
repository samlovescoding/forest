<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appearance extends Model
{
  protected $fillable = [
      'title',
      'slug',
      'type',
      'source',
      'person_id',
      'film_id',
      'show_id',
      'season_id',
      'episode_id',
  ];

  public function person()
  {
    return $this->belongsTo(Person::class);
  }

  public function film()
  {
    return $this->belongsTo(Film::class);
  }

  public function show()
  {
    return $this->belongsTo(Show::class);
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  public function episode()
  {
    return $this->belongsTo(Episode::class);
  }
}
