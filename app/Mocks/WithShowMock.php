<?php

namespace App\Mocks;

trait WithShowMock
{
  public function prefill(): void
  {
    $faker = fake();
    $name = $faker->words($faker->numberBetween(1, 4), true);
    $firstAirDate = $faker->dateTimeBetween('-30 years', '-1 year');
    $lastAirDate = $faker->boolean(70)
      ? $faker->dateTimeBetween($firstAirDate, 'now')
      : null;

    $this->name = ucwords($name);
    $this->slug = str($name)->slug();
    $this->overview = $faker->paragraphs($faker->numberBetween(1, 3), true);
    $this->episode_run_time = $faker->randomElement([22, 30, 42, 45, 50, 60]);
    $this->number_of_seasons = $faker->numberBetween(1, 12);
    $this->number_of_episodes = $this->number_of_seasons * $faker->numberBetween(8, 24);
    $this->first_air_date = $firstAirDate->format('Y-m-d');
    $this->last_air_date = $lastAirDate?->format('Y-m-d') ?? '';
    $this->tmdb_id = $faker->numberBetween(1000, 999999);
    $this->imdb_id = 'tt'.$faker->numerify('#######');
  }
}
