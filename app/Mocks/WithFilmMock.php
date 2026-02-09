<?php

namespace App\Mocks;

trait WithFilmMock
{
  public function prefill(): void
  {
    $faker = fake();
    $title = $faker->words($faker->numberBetween(1, 4), true);
    $releaseDate = $faker->dateTimeBetween('-50 years', 'now');

    $this->title = ucwords($title);
    $this->slug = str($title)->slug();
    $this->overview = $faker->paragraphs($faker->numberBetween(1, 3), true);
    $this->runtime = $faker->numberBetween(80, 180);
    $this->release_date = $releaseDate->format('Y-m-d');
    $this->tmdb_id = $faker->numberBetween(1000, 999999);
    $this->imdb_id = 'tt'.$faker->numerify('#######');
  }
}
