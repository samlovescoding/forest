<?php

use App\Models\Season;
use App\Models\Show;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
  use WithPagination;

  public Show $show;

  public Season $season;

  #[Url(except: '')]
  public string $query = '';

  #[Url(except: false)]
  public bool $hidden = false;

  public function mount(Show $show, Season $season): void
  {
    $this->show = $show;
    $this->season = $season;
  }

  #[Computed]
  public function episodes(): LengthAwarePaginator
  {
    $search = $this->query;

    return $this->season->episodes()
        ->when($search != '', function ($query) use ($search) {
          return $query->whereLike('name', '%'.$search.'%');
        })
        ->when($this->hidden === false, function ($query) {
          $query
              ->where('is_published', '=', true)
              ->where('is_hidden', '=', false);
        })
        ->orderBy('episode_number', 'asc')
        ->paginate(12);
  }

  public function toggleVisibility(): void
  {
    $this->hidden = ! $this->hidden;
  }
};
?>

<div>
  <title>{{ $this->season->name }} &bull; Episodes</title>

  <div class="flex flex-row items-center justify-between gap-4">
    <div>
      <flux:heading size="xl" level="1">{{ $this->show->name }} &mdash; {{ $this->season->name }} &mdash; Episodes</flux:heading>
      <flux:text class="mt-2 text-base">Manage episodes for this season.</flux:text>
    </div>

    <div class="flex gap-2">
      <flux:button
        tooltip="Toggle unlisted and unpublished episodes"
        size="sm" wire:click="toggleVisibility">
        <flux:icon.eye :variant="$this->hidden ? 'solid' : 'outline'"/>
      </flux:button>
      <flux:button
        href="{{ route('episodes.create', [$this->show, $this->season]) }}"
        tooltip="Add new episode"
        size="sm" wire:navigate>
        <flux:icon name="pencil-square"/>
      </flux:button>
      <flux:input wire:model.live.debounce.1000ms="query"
                  size="sm" placeholder="Search" clearable/>
    </div>
  </div>

  <flux:separator variant="subtle" class="my-6"/>

  @if($this->episodes->isEmpty())
    <flux:callout icon="film" variant="secondary" heading="No episodes added yet.">
      Start by adding the first episode.
    </flux:callout>
  @else
    <flux:table>
      <flux:table.columns>
        <flux:table.column>#</flux:table.column>
        <flux:table.column>Still</flux:table.column>
        <flux:table.column>Name</flux:table.column>
        <flux:table.column>Runtime</flux:table.column>
        <flux:table.column>Air Date</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->episodes as $episode)
          <flux:table.row :key="$episode->id">
            <flux:table.cell>{{ $episode->episode_number }}</flux:table.cell>
            <flux:table.cell>
              <div class="w-24 aspect-video overflow-hidden rounded bg-zinc-100 dark:bg-white/10">
                <x-picture
                  :src="$episode->stillUrl(...)"
                  :alt="$episode->name"
                  icon="film"/>
              </div>
            </flux:table.cell>
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

    <flux:pagination :paginator="$this->episodes" class="mt-6"/>
  @endif
</div>
