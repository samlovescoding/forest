<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PersonForm extends Form
{
  #[Validate('required|min:2')]
  public string $name;

  #[Validate('string')]
  public string $full_name;

  #[Validate('required|date')]
  public string $birth_date;

  #[Validate('nullable|date')]
  public string $death_date; // nullable date

  #[Validate('required')]
  public string $gender;

  #[Validate('required')]
  public string $sexuality;

  #[Validate('required')]
  public string $birth_country;

  #[Validate('required')]
  public string $birth_city;

  // #[Validate('required|image|max:10240')]
  public string $picture;

  public function all()
  {
    return $this->validate();
  }
}
