<?php

namespace App\Livewire\Forms;

use App\Mocks\WithSeasonMock;
use App\Models\Season;
use App\Models\Show;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class SeasonForm extends Form
{
  use WithSeasonMock;

  public ?Season $season = null;

  #[Validate('required|min:2')]
  public string $name = '';

  #[Validate('nullable|string')]
  public string $slug = '';

  #[Validate('nullable|string')]
  public string $overview = '';

  #[Validate('required|integer|min:0')]
  public ?int $season_number = null;

  #[Validate('nullable|integer|min:0')]
  public ?int $episode_count = null;

  #[Validate('nullable|date')]
  public string $air_date = '';

  #[Validate('nullable|integer')]
  public ?int $tmdb_id = null;

  #[Validate('nullable|image|max:10240')]
  public $poster;

  public function store(Show $show): Season
  {
    $fields = $this->validate();

    $slug = Season::createScopedSlug($fields['name'], $show->id);
    $fields['slug'] = $slug;
    $fields['poster_path'] = $this->storePoster($slug);

    unset($fields['poster']);

    return $show->seasons()->create($fields);
  }

  public function setSeason(Season $season): void
  {
    $this->season = $season;
    $this->name = $season->name;
    $this->slug = $season->slug;
    $this->overview = $season->overview ?? '';
    $this->season_number = $season->season_number;
    $this->episode_count = $season->episode_count;
    $this->air_date = $season->air_date?->format('Y-m-d') ?? '';
    $this->tmdb_id = $season->tmdb_id;
    $this->poster = null;
  }

  public function update(): Season
  {
    $fields = $this->validate();
    unset($fields['slug']);
    $slug = $this->season->slug;
    $fields['poster_path'] = $this->storePoster($slug, $this->season->poster_path);

    unset($fields['poster']);

    $this->season->update($fields);

    return $this->season->fresh();
  }

  public function storePoster(string $slug, ?string $default = null): ?string
  {
    if (! $this->poster instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'seasons';

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
}
