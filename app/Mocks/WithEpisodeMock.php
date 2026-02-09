<?php

namespace App\Mocks;

trait WithEpisodeMock
{
  public function prefill(): void
  {
    $faker = fake();
    $name = ucwords($faker->words($faker->numberBetween(2, 5), true));

    $this->name = $name;
    $this->slug = str($name)->slug();
    $this->overview = $faker->paragraphs($faker->numberBetween(1, 3), true);
    $this->episode_number = $faker->numberBetween(1, 24);
    $this->season_number = $faker->numberBetween(1, 10);
    $this->runtime = $faker->randomElement([22, 30, 42, 45, 50, 60]);
    $this->air_date = $faker->dateTimeBetween('-20 years', 'now')->format('Y-m-d');
    $this->production_code = strtoupper($faker->lexify('???')).$faker->numerify('###');
    $this->tmdb_id = $faker->numberBetween(1000, 999999);
  }
}
