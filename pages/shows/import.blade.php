<?php

use App\Jobs\ImportShowSeasonsAndEpisodes;
use App\Models\Genre;
use App\Models\Show;
use App\Services\TMDbService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
  #[Url(except: '')]
  public string $query = '';

  public $response = null;

  public function search(): void
  {
    $tmdb = new TMDbService;
    $this->response = $tmdb->searchTvShows($this->query);
  }

  public function import(int $id)
  {
    $existing = Show::query()->where('tmdb_id', $id)->first();

    if ($existing) {
      $this->redirectRoute('shows.view', $existing);

      return;
    }

    $tmdb = new TMDbService;
    $showData = $tmdb->getTvShow($id, ['external_ids']);

    $slug = Show::createSlug($showData['name']);

    $posterPath = $this->storeImageFromUrl(
      TMDbService::imageUrl($showData['poster_path']),
      $slug.'-poster',
      1000,
      1500,
    );

    $backdropPath = $this->storeImageFromUrl(
      TMDbService::imageUrl($showData['backdrop_path']),
      $slug.'-backdrop',
      1920,
      1080,
    );

    $genreIds = Genre::query()
        ->whereIn('tmdb_id', collect($showData['genres'] ?? [])->pluck('id'))
        ->pluck('id')
        ->toArray();

    $episodeRunTime = $showData['episode_run_time'][0] ?? null;

    $show = Show::query()->create([
        'name' => $showData['name'],
        'slug' => $slug,
        'overview' => $showData['overview'],
        'episode_run_time' => $episodeRunTime,
        'number_of_seasons' => $showData['number_of_seasons'],
        'number_of_episodes' => $showData['number_of_episodes'],
        'first_air_date' => $showData['first_air_date'],
        'last_air_date' => $showData['last_air_date'],
        'tmdb_id' => $showData['id'],
        'imdb_id' => $showData['external_ids']['imdb_id'] ?? null,
        'poster_path' => $posterPath,
        'backdrop_path' => $backdropPath,
        'vote_count' => $showData['vote_count'],
        'vote_average' => $showData['vote_average'],
        'popularity' => $showData['popularity'],
    ]);

    $show->genres()->sync($genreIds);

    ImportShowSeasonsAndEpisodes::dispatch($show);

    $this->redirectRoute('shows.view', $show);
  }

  private function storeImageFromUrl(string $url, string $name, int $width, int $height): ?string
  {
    $response = Http::get($url);

    if ($response->failed()) {
      return null;
    }

    $imageContent = $response->body();
    $manager = new ImageManager(new Driver);
    $storage = Storage::disk('public');

    $fileBase = 'shows/'.$name.'.';
    $primaryFileName = $fileBase.'jpg';

    $source = $manager->read($imageContent)->coverDown($width, $height, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toAvif(65));
    $storage->put($fileBase.'webp', $source->toWebp(70));
    Storage::disk('local')->put($primaryFileName, $imageContent);

    return $primaryFileName;
  }
};
?>

<main>
  <x-form class="flex gap-2" wire:submit="search">
    <div>
      <flux:input wire:model="query" placeholder="Search for TV shows..."/>
    </div>
    <flux:button type="submit">Search</flux:button>
  </x-form>

  @isset($this->response)
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 mt-8">
      @foreach ($this->response['results'] as $show)
        <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
          <div class="aspect-2/3 w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
            <flux:badge size="sm" class="absolute top-2 right-2">{{ $show['first_air_date'] }}</flux:badge>
            <img
              src="{{ TMDbService::imageUrl($show['poster_path'])  }}"
              alt="Poster of {{ $show['name'] }}"
            />
          </div>
          <div class="min-w-0 p-4 pt-0 flex flex-col gap-4">
            <flux:heading class="truncate">
              {{ $show['name'] }}
            </flux:heading>
            <div>
              <flux:button size="xs"
                           wire:confirm="Are you sure, you want to import this show?"
                           wire:click="import({{ $show['id'] }})">
                Import
              </flux:button>
            </div>
          </div>
        </flux:card>
      @endforeach
    </div>
  @endisset
</main>
