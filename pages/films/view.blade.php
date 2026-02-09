<?php

use App\Models\Film;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  public Film $film;

  public function mount(Film $film): void
  {
    $this->film = $film;
  }

  #[Computed]
  public function formattedRuntime(): string
  {
    if (! $this->film->runtime) {
      return 'N/A';
    }

    $hours = floor($this->film->runtime / 60);
    $minutes = $this->film->runtime % 60;

    if ($hours > 0) {
      return "{$hours}h {$minutes}m";
    }

    return "{$minutes}m";
  }
};
?>

<div class="space-y-6">
  <title>{{ $this->film->title }} &bull; Film View</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem_minmax(0,1fr)]">
    <div class="flex flex-col gap-4">
      <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
        <div class="relative aspect-[2/3] bg-zinc-100 dark:bg-white/10">
          @if($this->film->poster_path)
          <img
            src="{{ Storage::disk('public')->url($this->film->poster_path) }}"
            alt="{{ $this->film->title }}"
            class="size-full object-cover" />
          @else
          <div class="flex size-full items-center justify-center">
            <flux:icon.film class="size-12 text-zinc-400" />
          </div>
          @endif
        </div>

        <div class="space-y-3 p-6 pt-0">
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Title</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->film->title }}</dd>
          </div>
          @if($this->film->release_date)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Release Date</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->film->release_date->toFormattedDateString() }}</dd>
          </div>
          @endif
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Runtime</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->formattedRuntime }}</dd>
          </div>
          @if($this->film->vote_average)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Rating</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->film->vote_average, 1) }}/10</dd>
          </div>
          @endif
          @if($this->film->vote_count)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Votes</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->film->vote_count) }}</dd>
          </div>
          @endif
          @if($this->film->popularity)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Popularity</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->film->popularity, 1) }}</dd>
          </div>
          @endif
        </div>
      </flux:card>
      <div class="flex">
        <flux:button size="sm" href="{{ route('films.edit', $this->film) }}" wire:navigate>
          Edit Details
        </flux:button>
      </div>
    </div>

    <div class="space-y-6">
      @if($this->film->backdrop_path)
      <div class="aspect-video overflow-hidden rounded-lg bg-zinc-100 dark:bg-white/10">
        <img
          src="{{ Storage::disk('public')->url($this->film->backdrop_path) }}"
          alt="{{ $this->film->title }} backdrop"
          class="size-full object-cover" />
      </div>
      @endif

      @if($this->film->overview)
      <flux:card>
        <flux:heading size="sm" class="mb-2">Overview</flux:heading>
        <flux:text>{{ $this->film->overview }}</flux:text>
      </flux:card>
      @endif

      @if($this->film->tmdb_id || $this->film->imdb_id)
      <flux:card>
        <flux:heading size="sm" class="mb-2">External IDs</flux:heading>
        <div class="flex gap-4">
          @if($this->film->tmdb_id)
          <flux:text>
            <span class="text-zinc-500">TMDB:</span> {{ $this->film->tmdb_id }}
          </flux:text>
          @endif
          @if($this->film->imdb_id)
          <flux:text>
            <span class="text-zinc-500">IMDB:</span> {{ $this->film->imdb_id }}
          </flux:text>
          @endif
        </div>
      </flux:card>
      @endif
    </div>
  </section>
</div>
