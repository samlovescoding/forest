<?php

namespace App\Mocks;

trait WithSeasonMock
{
  public function prefill(): void
  {
    $faker = fake();
    $seasonNumber = $faker->numberBetween(1, 10);
    $name = 'Season '.$seasonNumber;

    $this->name = $name;
    $this->slug = str($name)->slug();
    $this->overview = $faker->paragraphs($faker->numberBetween(1, 3), true);
    $this->season_number = $seasonNumber;
    $this->episode_count = $faker->numberBetween(6, 24);
    $this->air_date = $faker->dateTimeBetween('-20 years', 'now')->format('Y-m-d');
    $this->tmdb_id = $faker->numberBetween(1000, 999999);
  }
}
