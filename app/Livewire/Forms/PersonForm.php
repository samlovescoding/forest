<?php

namespace App\Livewire\Forms;

use App\Models\Person;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PersonForm extends Form
{
  public ?Person $person = null;

  #[Validate('required|min:2')]
  public string $name = '';

  #[Validate('nullable|string')]
  public string $slug = '';

  #[Validate('nullable|string')]
  public string $full_name = '';

  #[Validate('required|date')]
  public string $birth_date = '';

  #[Validate('nullable|date')]
  public string $death_date = '';

  #[Validate('required')]
  public string $gender = 'female';

  #[Validate('required')]
  public string $sexuality = 'straight';

  #[Validate('required')]
  public string $birth_country = 'United States of America';

  #[Validate('nullable|string')]
  public string $birth_city = '';

  #[Validate('nullable|image|max:10240')]
  public $picture;

  public function prefill()
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

  public function all()
  {
    $fields = $this->validate();

    return $fields;
  }

  public function store()
  {
    $fields = $this->validate();
    $fields['slug'] = Person::createSlug($fields['name']);
    dd($fields);

    return $fields;
  }

  public function setPerson(Person $person): void
  {
    $this->person = $person;
    $this->name = $person->name;
    $this->slug = $person->slug;
    $this->full_name = $person->full_name;
    $this->birth_date = $person->birth_date->format('Y-m-d');
    $this->death_date = $person->death_date?->format('Y-m-d') ?? '';
    $this->gender = $person->gender;
    $this->sexuality = $person->sexuality;
    $this->birth_country = $person->birth_country;
    $this->birth_city = $person->birth_city;
    $this->picture = null;
  }

  public function update(): Person
  {
    $fields = $this->validate();

    // Slug is only created and cannot be updated.
    unset($fields['slug']);

    if ($this->picture) {
      $fields['picture'] = $this->picture->store('people', 'public');
    }

    $this->person->update($fields);

    return $this->person->fresh();
  }
}
