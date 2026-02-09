<?php

namespace App\Mocks;

trait WithPersonMock
{
  public function prefill(): void
  {
    $faker = fake();
    $displayName = $faker->firstName().' '.$faker->lastName();
    $birthDate = $faker->dateTimeBetween('-95 years', '-18 years');

    $this->name = $displayName;
    $this->full_name = $faker->name();
    $this->slug = str($displayName)->slug();
    $this->birth_date = $birthDate->format('Y-m-d');
    $this->death_date = $faker->boolean(20)
      ? $faker->dateTimeBetween($birthDate, 'now')->format('Y-m-d')
      : '';
    $this->gender = $faker->randomElement(['female', 'male', 'unknown']);
    $this->sexuality = $faker->randomElement([
        'straight',
        'lesbian',
        'gay',
        'trans-male',
        'trans-female',
        'bisexual-male',
        'bisexual-female',
    ]);
    $this->birth_country = $faker->country();
    $this->birth_city = $faker->city();
  }
}
