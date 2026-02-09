<?php

use App\Models\Season;
use App\Models\Show;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  public Show $show;

  public Season $season;

  public function mount(Show $show, Season $season): void
  {
    $this->show = $show;
    $this->season = $season;
  }

  #[Computed]
  public function episodes(): Collection
  {
    return $this->season->episodes()->orderBy('episode_number')->get();
  }
};
?>

<div class="space-y-6">
  <title>{{ $this->season->name }} &bull; {{ $this->show->name }}</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem_minmax(0,1fr)]">
    <div class="flex flex-col gap-4">
      <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
        <div class="relative aspect-[2/3] bg-zinc-100 dark:bg-white/10">
          <x-picture
            :src="$this->season->posterUrl(...)"
            :alt="$this->season->name"
            icon="tv"
          />
        </div>

        <div class="space-y-3 p-6 pt-0">
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Name</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->season->name }}</dd>
          </div>
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Season</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->season->season_number }}</dd>
          </div>
          @if($this->season->episode_count)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Episodes</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->season->episode_count }}</dd>
          </div>
          @endif
          @if($this->season->air_date)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Air Date</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->season->air_date->format('M d, Y') }}</dd>
          </div>
          @endif
          @if($this->season->vote_average)
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Rating</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($this->season->vote_average, 1) }}/10</dd>
          </div>
          @endif
        </div>
      </flux:card>
      <div class="flex">
        <flux:button size="sm" href="{{ route('seasons.edit', [$this->show, $this->season]) }}" wire:navigate>
          Edit Details
        </flux:button>
      </div>
    </div>

    <div class="space-y-6">
      @if($this->season->overview)
      <flux:card>
        <flux:heading size="sm" class="mb-2">Overview</flux:heading>
        <flux:text>{{ $this->season->overview }}</flux:text>
      </flux:card>
      @endif

      @if($this->season->tmdb_id)
      <flux:card>
        <flux:heading size="sm" class="mb-2">External IDs</flux:heading>
        <div class="flex gap-4">
          <flux:text>
            <span class="text-zinc-500">TMDB:</span> {{ $this->season->tmdb_id }}
          </flux:text>
        </div>
      </flux:card>
      @endif

      <flux:card>
        <div class="flex items-center justify-between mb-4">
          <flux:heading size="sm">Episodes</flux:heading>
          <div class="flex gap-2">
            <flux:button size="sm" href="{{ route('episodes.create', [$this->show, $this->season]) }}" wire:navigate>
              Add Episode
            </flux:button>
            <flux:button size="sm" variant="ghost" href="{{ route('episodes.index', [$this->show, $this->season]) }}" wire:navigate>
              View All
            </flux:button>
          </div>
        </div>

        @if($this->episodes->isEmpty())
          <flux:text class="text-zinc-500">No episodes added yet.</flux:text>
        @else
          <flux:table>
            <flux:table.columns>
              <flux:table.column>#</flux:table.column>
              <flux:table.column>Name</flux:table.column>
              <flux:table.column>Runtime</flux:table.column>
              <flux:table.column>Air Date</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
              @foreach($this->episodes as $episode)
                <flux:table.row :key="$episode->id">
                  <flux:table.cell>{{ $episode->episode_number }}</flux:table.cell>
                  <flux:table.cell>
                    <a href="{{ route('episodes.view', [$this->show, $this->season, $episode]) }}" wire:navigate class="text-accent hover:underline">
                      {{ $episode->name }}
                    </a>
                  </flux:table.cell>
                  <flux:table.cell>{{ $episode->runtime ? $episode->runtime.'m' : 'N/A' }}</flux:table.cell>
                  <flux:table.cell>{{ $episode->air_date?->format('M d, Y') ?? 'N/A' }}</flux:table.cell>
                </flux:table.row>
              @endforeach
            </flux:table.rows>
          </flux:table>
        @endif
      </flux:card>
    </div>
  </section>
</div>
