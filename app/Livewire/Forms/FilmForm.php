<?php

namespace App\Livewire\Forms;

use App\Mocks\WithFilmMock;
use App\Models\Film;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class FilmForm extends Form
{
  use WithFilmMock;

  public ?Film $film = null;

  #[Validate('required|min:2')]
  public string $title = '';

  #[Validate('nullable|string')]
  public string $slug = '';

  #[Validate('nullable|string')]
  public string $overview = '';

  #[Validate('nullable|integer|min:0')]
  public ?int $runtime = null;

  #[Validate('nullable|date')]
  public string $release_date = '';

  #[Validate('nullable|integer')]
  public ?int $tmdb_id = null;

  #[Validate('nullable|string')]
  public string $imdb_id = '';

  #[Validate('nullable|image|max:10240')]
  public $poster;

  #[Validate('nullable|image|max:10240')]
  public $backdrop;

  public function store(): Film
  {
    $fields = $this->validate();

    $slug = Film::createSlug($fields['title']);
    $fields['slug'] = $slug;
    $fields['poster_path'] = $this->storePoster($slug);
    $fields['backdrop_path'] = $this->storeBackdrop($slug);

    unset($fields['poster'], $fields['backdrop']);

    return Film::query()->create($fields);
  }

  public function setFilm(Film $film): void
  {
    $this->film = $film;
    $this->title = $film->title;
    $this->slug = $film->slug;
    $this->overview = $film->overview ?? '';
    $this->runtime = $film->runtime;
    $this->release_date = $film->release_date?->format('Y-m-d') ?? '';
    $this->tmdb_id = $film->tmdb_id;
    $this->imdb_id = $film->imdb_id ?? '';
    $this->poster = null;
    $this->backdrop = null;
  }

  public function update(): Film
  {
    $fields = $this->validate();
    unset($fields['slug']);
    $slug = $this->film->slug;
    $fields['poster_path'] = $this->storePoster($slug, $this->film->poster_path);
    $fields['backdrop_path'] = $this->storeBackdrop($slug, $this->film->backdrop_path);

    unset($fields['poster'], $fields['backdrop']);

    $this->film->update($fields);

    return $this->film->fresh();
  }

  public function storePoster(string $slug, ?string $default = null): ?string
  {
    if (! $this->poster instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'films';

    $fileBase = $folder.'/'.$slug.'-poster.';
    $extension = $this->poster->getClientOriginalExtension();
    $primaryFileName = $fileBase.$extension;

    $source = $manager->read($this->poster)->coverDown(500, 750, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toAvif(65));
    $storage->put($fileBase.'webp', $source->toWebp(70));
    Storage::disk('local')->put($primaryFileName, $this->poster->getContent());

    return $primaryFileName;
  }

  public function storeBackdrop(string $slug, ?string $default = null): ?string
  {
    if (! $this->backdrop instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'films';

    $fileBase = $folder.'/'.$slug.'-backdrop.';
    $extension = $this->backdrop->getClientOriginalExtension();
    $primaryFileName = $fileBase.$extension;

    $source = $manager->read($this->backdrop)->coverDown(1920, 1080, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toAvif(65));
    $storage->put($fileBase.'webp', $source->toWebp(70));
    Storage::disk('local')->put($primaryFileName, $this->backdrop->getContent());

    return $primaryFileName;
  }
}
