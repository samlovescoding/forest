<?php

namespace App\Livewire\Forms;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
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

  public function all(): array
  {
    $fields = $this->validate();

    return $fields;
  }

  public function store(): Person
  {
    $fields = $this->validate();

    $slug = Person::createSlug($fields['name']);
    $fields['slug'] = $slug;
    $fields['picture'] = $this->storePicture($slug);

    return Person::query()->create($fields);
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
    unset($fields['slug']);
    $slug = $this->person->slug;
    $fields['picture'] = $this->storePicture($slug, $this->person->picture);
    $this->person->update($fields);

    return $this->person->fresh();
  }

  public function storePicture($slug, $default = null)
  {
    // Uses the $slug to create /person/slug.jpg.
    // Returns $default when no image is sent. (Saves a nasty if else)

    if (! $this->picture instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'people';

    $fileBase = $folder.'/'.$slug.'.';
    $extension = $this->picture->getClientOriginalExtension();
    $primaryFileName = $fileBase.$extension;

    $source = $manager->read($this->picture)->coverDown(768, 768, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toWebp(70));
    $storage->put($fileBase.'webp', $source->toAvif(65));
    Storage::disk('local')->put($primaryFileName, $this->picture->getContent());

    return $primaryFileName;
  }
}
