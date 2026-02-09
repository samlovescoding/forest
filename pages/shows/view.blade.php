<?php

use App\Models\Show;
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
};
?>

<div class="space-y-6">
  <title>{{ $this->show->name }} &bull; Show View</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem_minmax(0,1fr)]">
    <div class="flex flex-col gap-4">
      <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
        <div class="relative aspect-[2/3] bg-zinc-100 dark:bg-white/10">
          @if($this->show->poster_path)
          <img
            src="{{ Storage::disk('public')->url($this->show->poster_path) }}"
            alt="{{ $this->show->name }}"
            class="size-full object-cover" />
          @else
          <div class="flex size-full items-center justify-center">
            <flux:icon.tv class="size-12 text-zinc-400" />
          </div>
          @endif
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
        <img
          src="{{ Storage::disk('public')->url($this->show->backdrop_path) }}"
          alt="{{ $this->show->name }} backdrop"
          class="size-full object-cover" />
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
    </div>
  </section>
</div>
