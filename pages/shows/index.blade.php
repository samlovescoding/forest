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

  #[Url(except: '')]
  public string $query = '';

  #[Url(except: false)]
  public bool $hidden = false;

  #[Computed]
  public function shows(): LengthAwarePaginator
  {
    $search = $this->query;

    return Show::query()
        ->when($search != '', function ($query) use ($search) {
          return $query->whereLike('name', '%'.$search.'%');
        })
        ->when($this->hidden === false, function ($query) {
          $query
              ->where('is_published', '=', true)
              ->where('is_hidden', '=', false);
        })
        ->orderBy('name', 'asc')
        ->paginate(12);
  }

  public function toggleVisibility(): void
  {
    $this->hidden = ! $this->hidden;
  }
};
?>

<div>
  <title>Shows</title>

  <div class="flex flex-row items-center justify-between gap-4">
    <div>
      <flux:heading size="xl" level="1">Shows</flux:heading>
      <flux:text class="mt-2 text-base">Browse all TV shows.</flux:text>
    </div>

    <div class="flex gap-2">
      <flux:button
        tooltip="Toggle unlisted and unpublished shows"
        size="sm" wire:click="toggleVisibility">
        <flux:icon.eye :variant="$this->hidden ? 'solid' : 'outline'"/>
      </flux:button>
      <flux:button
        href="{{ route('shows.import') }}"
        tooltip="Import from TMDb"
        size="sm" wire:navigate>
        <flux:icon name="arrow-down-tray"/>
      </flux:button>
      <flux:button
        href="{{ route('shows.create') }}"
        tooltip="Manually add new show"
        size="sm" wire:navigate>
        <flux:icon name="pencil-square"/>
      </flux:button>
      <flux:input wire:model.live.debounce.1000ms="query"
                  wire:loading.class=""
                  size="sm" placeholder="Search" clearable/>
    </div>
  </div>

  <flux:separator variant="subtle" class="my-6"/>

  @if($this->shows->isEmpty())
    <flux:callout icon="tv" variant="secondary" heading="No shows added yet.">
      Start by adding your first show.
    </flux:callout>
  @else
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
      @foreach($this->shows as $show)
        <a href="{{ route('shows.view', $show) }}" wire:navigate class="block h-full">
          <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
            <div class="aspect-2/3 w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
              @if($show->first_air_date)
                <flux:badge size="sm"
                            class="absolute top-2 right-2">{{ $show->first_air_date->format('Y') }}</flux:badge>
              @endif
              <x-picture
                :src="$show->posterUrl(...)"
                :alt="$show->name"
                icon="tv"/>
            </div>
            <div class="min-w-0 p-4 pt-0">
              <flux:heading size="sm" class="truncate">{{ $show->name }}</flux:heading>
            </div>
          </flux:card>
        </a>
      @endforeach
    </div>

    <flux:pagination :paginator="$this->shows" class="mt-6"/>
  @endif
</div>
