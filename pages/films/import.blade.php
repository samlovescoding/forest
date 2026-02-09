<?php

use App\Models\Film;
use App\Models\Genre;
use App\Services\TMDbService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
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
    $this->response = $tmdb->searchMovies($this->query);
  }

  public function import(int $id)
  {
    $existing = Film::query()->where('tmdb_id', $id)->first();

    if ($existing) {
      $this->redirectRoute('films.view', $existing);

      return;
    }

    $tmdb = new TMDbService;
    $movieData = $tmdb->getMovie($id);

    $slug = Film::createSlug($movieData['title']);

    $posterPath = $this->storeImageFromUrl(
      TMDbService::imageUrl($movieData['poster_path']),
      $slug.'-poster',
      1000,
      1500,
    );

    $backdropPath = $this->storeImageFromUrl(
      TMDbService::imageUrl($movieData['backdrop_path']),
      $slug.'-backdrop',
      1920,
      1080,
    );

    $genreIds = Genre::query()
        ->whereIn('tmdb_id', collect($movieData['genres'] ?? [])->pluck('id'))
        ->pluck('id')
        ->toArray();

    $film = Film::query()->create([
        'title' => $movieData['title'],
        'slug' => $slug,
        'overview' => $movieData['overview'],
        'runtime' => $movieData['runtime'],
        'release_date' => $movieData['release_date'],
        'tmdb_id' => $movieData['id'],
        'imdb_id' => $movieData['imdb_id'],
        'poster_path' => $posterPath,
        'backdrop_path' => $backdropPath,
        'vote_count' => $movieData['vote_count'],
        'vote_average' => $movieData['vote_average'],
        'popularity' => $movieData['popularity'],
    ]);

    $film->genres()->sync($genreIds);

    $this->redirectRoute('films.view', $film);
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

    $fileBase = 'films/'.$name.'.';
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
      <flux:input wire:model="query" placeholder="Search for movies..."/>
    </div>
    <flux:button type="submit">Search</flux:button>
  </x-form>

  @isset($this->response)
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 mt-8">
      @foreach ($this->response['results'] as $film)
        <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
          <div class="aspect-2/3 w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
            <flux:badge size="sm" class="absolute top-2 right-2">{{ $film['release_date'] }}</flux:badge>
            <img
              src="{{ TMDbService::imageUrl($film['poster_path'])  }}"
              alt="Poster of {{ $film['title'] }}"
            />
          </div>
          <div class="min-w-0 p-4 pt-0 flex flex-col gap-4">
            <flux:heading class="truncate">
              {{ $film['title'] }}
            </flux:heading>
            <div>
              <flux:button size="xs"
                           wire:confirm="Are you sure, you want to import this movie?"
                           wire:click="import({{ $film['id'] }})">
                Import
              </flux:button>
            </div>
          </div>
        </flux:card>
      @endforeach
    </div>
  @endisset
</main>
