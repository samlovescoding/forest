<?php

use App\Models\Film;
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
  public function films(): LengthAwarePaginator
  {
    $search = $this->query;

    return Film::query()
        ->when($search != '', function ($query) use ($search) {
          return $query->whereLike('title', '%'.$search.'%');
        })
        ->when($this->hidden === false, function ($query) {
          $query
              ->where('is_published', '=', true)
              ->where('is_hidden', '=', false);
        })
        ->orderBy('title', 'asc')
        ->paginate(12);
  }

  public function toggleVisibility(): void
  {
    $this->hidden = ! $this->hidden;
  }
};
?>

<div>
  <title>Films</title>

  <div class="flex flex-row items-center justify-between gap-4">
    <div>
      <flux:heading size="xl" level="1">Films</flux:heading>
      <flux:text class="mt-2 text-base">Browse all films.</flux:text>
    </div>

    <div class="flex gap-2">
      <flux:button
        tooltip="Toggle unlisted and unpublished films"
        size="sm" wire:click="toggleVisibility">
        <flux:icon.eye :variant="$this->hidden ? 'solid' : 'outline'" />
      </flux:button>
      <flux:button size="sm" href="{{ route('films.create') }}" wire:navigate>Create</flux:button>
      <flux:input wire:model.live.debounce.1000ms="query"
        wire:loading.class=""
        size="sm" placeholder="Search" clearable />
    </div>
  </div>

  <flux:separator variant="subtle" class="my-6" />

  @if($this->films->isEmpty())
  <flux:callout icon="film" variant="secondary" heading="No films added yet.">
    Start by adding your first film.
  </flux:callout>
  @else
  <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
    @foreach($this->films as $film)
    <a href="{{ route('films.view', $film) }}" wire:navigate class="block h-full">
      <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
        <div class="aspect-[2/3] w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
          @if($film->release_date)
          <flux:badge size="sm" class="absolute top-2 right-2">{{ $film->release_date->format('Y') }}</flux:badge>
          @endif
          @if($film->poster_path)
          <img
            src="{{ Storage::disk('public')->url($film->poster_path) }}"
            alt="{{ $film->title }}"
            class="size-full object-cover" />
          @else
          <div class="flex size-full items-center justify-center">
            <flux:icon.film class="size-12 text-zinc-400" />
          </div>
          @endif

        </div>
        <div class="min-w-0 p-4 pt-0 flex justify-between items-center">
          <flux:heading class="truncate">{{ $film->title }}</flux:heading>
        </div>
      </flux:card>
    </a>
    @endforeach
  </div>

  <flux:pagination :paginator="$this->films" class="mt-6" />
  @endif
</div>
