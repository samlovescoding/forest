<?php

use App\Models\Show;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  public Show $show;

  public function mount(Show $show): void
  {
    $this->show = $show;
  }

  #[Computed]
  public function formattedRuntime(): string
  {
    if (! $this->show->episode_run_time) {
      return 'N/A';
    }

    return "{$this->show->episode_run_time}m per episode";
  }

  #[Computed]
  public function airDateRange(): string
  {
    if (! $this->show->first_air_date) {
      return 'N/A';
    }

    $start = $this->show->first_air_date->format('Y');
    $end = $this->show->last_air_date ? $this->show->last_air_date->format('Y') : 'Present';

    return "{$start} - {$end}";
  }

  #[Computed]
  public function seasons(): Collection
  {
    return $this->show->seasons()->orderBy('season_number')->get();
  }
};
?>

<div class="space-y-6">
  <title>{{ $this->show->name }} &bull; Show View</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem_minmax(0,1fr)]">
    <div class="flex flex-col gap-4">
      <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
        <div class="relative aspect-[2/3] bg-zinc-100 dark:bg-white/10">
          <x-picture
            :src="$this->show->posterUrl(...)"
            :alt="$this->show->name"
            icon="tv"
          />
        </div>

        <div class="space-y-3 p-6 pt-0">
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Name</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->show->name }}</dd>
          </div>
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Years</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->airDateRange }}</dd>
          </div>
          @if($this->show->number_of_seasons)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Seasons</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->show->number_of_seasons }}</dd>
          </div>
          @endif
          @if($this->show->number_of_episodes)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Episodes</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->show->number_of_episodes }}</dd>
          </div>
          @endif
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Runtime</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->formattedRuntime }}</dd>
          </div>
          @if($this->show->vote_average)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Rating</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->show->vote_average, 1) }}/10</dd>
          </div>
          @endif
          @if($this->show->vote_count)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Votes</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->show->vote_count) }}</dd>
          </div>
          @endif
          @if($this->show->popularity)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Popularity</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->show->popularity, 1) }}</dd>
          </div>
          @endif
        </div>
      </flux:card>
      <div class="flex">
        <flux:button size="sm" href="{{ route('shows.edit', $this->show) }}" wire:navigate>
          Edit Details
        </flux:button>
      </div>
    </div>

    <div class="space-y-6">
      @if($this->show->backdrop_path)
      <div class="aspect-video overflow-hidden rounded-lg bg-zinc-100 dark:bg-white/10">
        <x-picture
          :src="$this->show->backdropUrl(...)"
          :alt="$this->show->name . ' backdrop'"
          icon="photo"
        />
      </div>
      @endif

      @if($this->show->overview)
      <flux:card>
        <flux:heading size="sm" class="mb-2">Overview</flux:heading>
        <flux:text>{{ $this->show->overview }}</flux:text>
      </flux:card>
      @endif

      @if($this->show->genres->isNotEmpty())
      <flux:card>
        <flux:heading size="sm" class="mb-2">Genres</flux:heading>
        <div class="flex flex-wrap gap-2">
          @foreach($this->show->genres as $genre)
            <flux:badge>{{ $genre->name }}</flux:badge>
          @endforeach
        </div>
      </flux:card>
      @endif

      @if($this->show->tmdb_id || $this->show->imdb_id)
      <flux:card>
        <flux:heading size="sm" class="mb-2">External IDs</flux:heading>
        <div class="flex gap-4">
          @if($this->show->tmdb_id)
          <flux:text>
            <span class="text-zinc-500">TMDB:</span> {{ $this->show->tmdb_id }}
          </flux:text>
          @endif
          @if($this->show->imdb_id)
          <flux:text>
            <span class="text-zinc-500">IMDB:</span> {{ $this->show->imdb_id }}
          </flux:text>
          @endif
        </div>
      </flux:card>
      @endif

      <flux:card>
        <div class="flex items-center justify-between mb-4">
          <flux:heading size="sm">Seasons</flux:heading>
          <div class="flex gap-2">
            <flux:button size="sm" href="{{ route('seasons.create', $this->show) }}" wire:navigate>
              Add Season
            </flux:button>
            <flux:button size="sm" variant="ghost" href="{{ route('seasons.index', $this->show) }}" wire:navigate>
              View All
            </flux:button>
          </div>
        </div>

        @if($this->seasons->isEmpty())
          <flux:text class="text-zinc-500">No seasons added yet.</flux:text>
        @else
          <div class="grid grid-cols-3 gap-4 md:grid-cols-4 lg:grid-cols-6">
            @foreach($this->seasons as $season)
              <a href="{{ route('seasons.view', [$this->show, $season]) }}" wire:navigate class="block h-full">
                <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
                  <div class="aspect-2/3 w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
                    <flux:badge size="sm" class="absolute top-2 right-2">S{{ str_pad($season->season_number, 2, '0', STR_PAD_LEFT) }}</flux:badge>
                    <x-picture
                      :src="$season->posterUrl(...)"
                      :alt="$season->name"
                      icon="tv"/>
                  </div>
                  <div class="min-w-0 p-4 pt-0">
                    <flux:heading size="sm" class="truncate">{{ $season->name }}</flux:heading>
                  </div>
                </flux:card>
              </a>
            @endforeach
          </div>
        @endif
      </flux:card>
    </div>
  </section>
</div>
