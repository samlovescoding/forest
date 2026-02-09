<?php

namespace App\Livewire\Forms;

use App\Mocks\WithShowMock;
use App\Models\Show;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class ShowForm extends Form
{
  use WithShowMock;

  public ?Show $show = null;

  #[Validate('required|min:2')]
  public string $name = '';

  #[Validate('nullable|string')]
  public string $slug = '';

  #[Validate('nullable|string')]
  public string $overview = '';

  #[Validate('nullable|integer|min:0')]
  public ?int $episode_run_time = null;

  #[Validate('nullable|integer|min:0')]
  public ?int $number_of_seasons = null;

  #[Validate('nullable|integer|min:0')]
  public ?int $number_of_episodes = null;

  #[Validate('nullable|date')]
  public string $first_air_date = '';

  #[Validate('nullable|date')]
  public string $last_air_date = '';

  #[Validate('nullable|integer')]
  public ?int $tmdb_id = null;

  #[Validate('nullable|string')]
  public string $imdb_id = '';

  #[Validate('nullable|image|max:10240')]
  public $poster;

  #[Validate('nullable|image|max:10240')]
  public $backdrop;

  #[Validate('nullable|array')]
  public array $genres = [];

  public function store(): Show
  {
    $fields = $this->validate();

    $slug = Show::createSlug($fields['name']);
    $fields['slug'] = $slug;
    $fields['poster_path'] = $this->storePoster($slug);
    $fields['backdrop_path'] = $this->storeBackdrop($slug);

    unset($fields['poster'], $fields['backdrop'], $fields['genres']);

    $show = Show::query()->create($fields);
    $show->genres()->sync($this->genres);

    return $show;
  }

  public function setShow(Show $show): void
  {
    $this->show = $show;
    $this->name = $show->name;
    $this->slug = $show->slug;
    $this->overview = $show->overview ?? '';
    $this->episode_run_time = $show->episode_run_time;
    $this->number_of_seasons = $show->number_of_seasons;
    $this->number_of_episodes = $show->number_of_episodes;
    $this->first_air_date = $show->first_air_date?->format('Y-m-d') ?? '';
    $this->last_air_date = $show->last_air_date?->format('Y-m-d') ?? '';
    $this->tmdb_id = $show->tmdb_id;
    $this->imdb_id = $show->imdb_id ?? '';
    $this->genres = $show->genres->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    $this->poster = null;
    $this->backdrop = null;
  }

  public function update(): Show
  {
    $fields = $this->validate();
    unset($fields['slug']);
    $slug = $this->show->slug;
    $fields['poster_path'] = $this->storePoster($slug, $this->show->poster_path);
    $fields['backdrop_path'] = $this->storeBackdrop($slug, $this->show->backdrop_path);

    unset($fields['poster'], $fields['backdrop'], $fields['genres']);

    $this->show->update($fields);
    $this->show->genres()->sync($this->genres);

    return $this->show->fresh();
  }

  public function storePoster(string $slug, ?string $default = null): ?string
  {
    if (! $this->poster instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'shows';

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
    $folder = 'shows';

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
