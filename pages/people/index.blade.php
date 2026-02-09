<?php

use App\Models\Person;
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
  public function people(): LengthAwarePaginator
  {
    $search = $this->query;

    return Person::query()
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

  public function toggleVisibility()
  {
    $this->hidden = ! $this->hidden;
  }
};
?>

<div>
  <title>People</title>

  <div class="flex flex-row items-center justify-between gap-4">
    <div>
      <flux:heading size="xl" level="1">People</flux:heading>
      <flux:text class="mt-2 text-base">Browse all enrolled people.</flux:text>
    </div>

    <div class="flex gap-2">
      <flux:button
        tooltip="Toggle unlisted and unpublished people"
        size="sm" wire:click="toggleVisibility">
        <flux:icon.eye :variant="$this->hidden ? 'solid' : 'outline'"/>
      </flux:button>
      <flux:button
        href="{{ route('people.create') }}"
        tooltip="Manually add new person"
        size="sm" wire:navigate>
        <flux:icon name="pencil-square"/>
      </flux:button>
      <flux:input wire:model.live.debounce.1000ms="query"
                  wire:loading.class=""
                  size="sm" placeholder="Search" clearable/>
    </div>
  </div>

  <flux:separator variant="subtle" class="my-6"/>

  @if($this->people->isEmpty())
    <flux:callout icon="users" variant="secondary" heading="No people enrolled yet.">
      Start by adding your first person.
    </flux:callout>
  @else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
      @foreach($this->people as $person)
        <a href="{{ route('people.view', $person) }}" wire:navigate class="block h-full">
          <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0">
            <div class="aspect-square w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
              <x-picture
                :src="$person->pictureUrl(...)"
                :alt="$person->name"
                icon="user"/>
            </div>
            <div class="min-w-0 p-4 pt-0">
              <flux:heading size="sm" class="truncate">{{ $person->name }}</flux:heading>
            </div>
          </flux:card>
        </a>
      @endforeach
    </div>

    <flux:pagination :paginator="$this->people" class="mt-6"/>
  @endif
</div>
