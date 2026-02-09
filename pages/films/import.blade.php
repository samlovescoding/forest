<?php

use App\Services\TMDbService;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
  #[Url(except: '')]
  public string $query = '';

  public $response = null;

  public function mount()
  {
    if (app()->environment('local')) {
      $this->query = 'Blue is warmest color';
      $this->search();
    }
  }

  public function search()
  {
    $tmdb = new TMDbService;
    $this->response = $tmdb->searchMovies($this->query);
  }

  public function import(int $id)
  {
    $tmdb = new TMDbService;
    $film = $tmdb->getMovie($id);
    dd($film);
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
