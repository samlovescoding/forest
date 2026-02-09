<?php

use App\Models\Episode;
use App\Models\Season;
use App\Models\Show;
use Livewire\Component;

new class extends Component
{
  public Show $show;

  public Season $season;

  public Episode $episode;

  public function mount(Show $show, Season $season, Episode $episode): void
  {
    $this->show = $show;
    $this->season = $season;
    $this->episode = $episode;
  }
};
?>

<div class="space-y-6">
  <title>{{ $this->episode->name }} &bull; {{ $this->season->name }}</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[20rem_minmax(0,1fr)]">
    <div class="flex flex-col gap-4">
      <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
        <div class="relative aspect-video bg-zinc-100 dark:bg-white/10">
          <x-picture
            :src="$this->episode->stillUrl(...)"
            :alt="$this->episode->name"
            icon="film"
          />
        </div>

        <div class="space-y-3 p-6 pt-0">
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Name</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->episode->name }}</dd>
          </div>
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Episode</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">S{{ str_pad($this->episode->season_number, 2, '0', STR_PAD_LEFT) }}E{{ str_pad($this->episode->episode_number, 2, '0', STR_PAD_LEFT) }}</dd>
          </div>
          @if($this->episode->runtime)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Runtime</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->episode->runtime }}m</dd>
          </div>
          @endif
          @if($this->episode->air_date)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Air Date</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->episode->air_date->format('M d, Y') }}</dd>
          </div>
          @endif
          @if($this->episode->production_code)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Prod. Code</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->episode->production_code }}</dd>
          </div>
          @endif
          @if($this->episode->vote_average)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Rating</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->episode->vote_average, 1) }}/10</dd>
          </div>
          @endif
          @if($this->episode->vote_count)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Votes</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->episode->vote_count) }}</dd>
          </div>
          @endif
        </div>
      </flux:card>
      <div class="flex">
        <flux:button size="sm" href="{{ route('episodes.edit', [$this->show, $this->season, $this->episode]) }}" wire:navigate>
          Edit Details
        </flux:button>
      </div>
    </div>

    <div class="space-y-6">
      @if($this->episode->overview)
      <flux:card>
        <flux:heading size="sm" class="mb-2">Overview</flux:heading>
        <flux:text>{{ $this->episode->overview }}</flux:text>
      </flux:card>
      @endif

      @if($this->episode->tmdb_id)
      <flux:card>
        <flux:heading size="sm" class="mb-2">External IDs</flux:heading>
        <div class="flex gap-4">
          <flux:text>
            <span class="text-zinc-500">TMDB:</span> {{ $this->episode->tmdb_id }}
          </flux:text>
        </div>
      </flux:card>
      @endif
    </div>
  </section>
</div>
