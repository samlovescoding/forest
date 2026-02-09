<?php

namespace App\Livewire\Forms;

use App\Mocks\WithEpisodeMock;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class EpisodeForm extends Form
{
  use WithEpisodeMock;

  public ?Episode $episode = null;

  #[Validate('required|min:2')]
  public string $name = '';

  #[Validate('nullable|string')]
  public string $slug = '';

  #[Validate('nullable|string')]
  public string $overview = '';

  #[Validate('required|integer|min:1')]
  public ?int $episode_number = null;

  #[Validate('required|integer|min:0')]
  public ?int $season_number = null;

  #[Validate('nullable|integer|min:0')]
  public ?int $runtime = null;

  #[Validate('nullable|date')]
  public string $air_date = '';

  #[Validate('nullable|string')]
  public string $production_code = '';

  #[Validate('nullable|integer')]
  public ?int $tmdb_id = null;

  #[Validate('nullable|image|max:10240')]
  public $still;

  public function store(Season $season): Episode
  {
    $fields = $this->validate();

    $slug = Episode::createScopedSlug($fields['name'], $season->id);
    $fields['slug'] = $slug;
    $fields['still_path'] = $this->storeStill($slug);
    $fields['show_id'] = $season->show_id;

    unset($fields['still']);

    return $season->episodes()->create($fields);
  }

  public function setEpisode(Episode $episode): void
  {
    $this->episode = $episode;
    $this->name = $episode->name;
    $this->slug = $episode->slug;
    $this->overview = $episode->overview ?? '';
    $this->episode_number = $episode->episode_number;
    $this->season_number = $episode->season_number;
    $this->runtime = $episode->runtime;
    $this->air_date = $episode->air_date?->format('Y-m-d') ?? '';
    $this->production_code = $episode->production_code ?? '';
    $this->tmdb_id = $episode->tmdb_id;
    $this->still = null;
  }

  public function update(): Episode
  {
    $fields = $this->validate();
    unset($fields['slug']);
    $slug = $this->episode->slug;
    $fields['still_path'] = $this->storeStill($slug, $this->episode->still_path);

    unset($fields['still']);

    $this->episode->update($fields);

    return $this->episode->fresh();
  }

  public function storeStill(string $slug, ?string $default = null): ?string
  {
    if (! $this->still instanceof TemporaryUploadedFile) {
      return $default;
    }

    $manager = new ImageManager(new Driver);

    $storage = Storage::disk('public');
    $folder = 'episodes';

    $fileBase = $folder.'/'.$slug.'-still.';
    $extension = $this->still->getClientOriginalExtension();
    $primaryFileName = $fileBase.$extension;

    $source = $manager->read($this->still)->coverDown(1920, 1080, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toAvif(65));
    $storage->put($fileBase.'webp', $source->toWebp(70));
    Storage::disk('local')->put($primaryFileName, $this->still->getContent());

    return $primaryFileName;
  }
}
