<?php

namespace App\Livewire\Forms;

use App\Models\Person;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PersonForm extends Form
{
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

    #[Validate('required|image|max:10240')]
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
}
