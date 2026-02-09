<?php

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

  #[Url(except: '')]
  public string $query = '';

  #[Url(except: false)]
  public bool $hidden = false;

  public function mount(Show $show): void
  {
    $this->show = $show;
  }

  #[Computed]
  public function seasons(): LengthAwarePaginator
  {
    $search = $this->query;

    return $this->show->seasons()
        ->when($search != '', function ($query) use ($search) {
          return $query->whereLike('name', '%'.$search.'%');
        })
        ->when($this->hidden === false, function ($query) {
          $query
              ->where('is_published', '=', true)
              ->where('is_hidden', '=', false);
        })
        ->orderBy('season_number', 'asc')
        ->paginate(12);
  }

  public function toggleVisibility(): void
  {
    $this->hidden = ! $this->hidden;
  }
};
?>

<div>
  <title>{{ $this->show->name }} &bull; Seasons</title>

  <div class="flex flex-row items-center justify-between gap-4">
    <div>
      <flux:heading size="xl" level="1">{{ $this->show->name }} &mdash; Seasons</flux:heading>
      <flux:text class="mt-2 text-base">Manage seasons for this show.</flux:text>
    </div>

    <div class="flex gap-2">
      <flux:button
        tooltip="Toggle unlisted and unpublished seasons"
        size="sm" wire:click="toggleVisibility">
        <flux:icon.eye :variant="$this->hidden ? 'solid' : 'outline'"/>
      </flux:button>
      <flux:button
        href="{{ route('seasons.create', $this->show) }}"
        tooltip="Add new season"
        size="sm" wire:navigate>
        <flux:icon name="pencil-square"/>
      </flux:button>
      <flux:input wire:model.live.debounce.1000ms="query"
                  size="sm" placeholder="Search" clearable/>
    </div>
  </div>

  <flux:separator variant="subtle" class="my-6"/>

  @if($this->seasons->isEmpty())
    <flux:callout icon="tv" variant="secondary" heading="No seasons added yet.">
      Start by adding the first season.
    </flux:callout>
  @else
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
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

    <flux:pagination :paginator="$this->seasons" class="mt-6"/>
  @endif
</div>
