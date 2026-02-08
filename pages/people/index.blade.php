<?php

use App\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
  use WithPagination;

  #[Computed]
  public function people(): LengthAwarePaginator
  {
    return Person::query()
        ->orderBy('name', 'asc')
        ->paginate(12);
  }
};
?>

<div>
  <title>People</title>

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <flux:heading size="xl" level="1">People</flux:heading>
      <flux:text class="mt-2 text-base">Browse all enrolled people.</flux:text>
    </div>

    <flux:button size="sm" href="{{ route('people.create') }}" wire:navigate>Add a Person</flux:button>
  </div>

  <flux:separator variant="subtle" class="my-6" />

  @if($this->people->isEmpty())
  <flux:callout icon="users" variant="secondary" heading="No people enrolled yet.">
    Start by adding your first person.
  </flux:callout>
  @else
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
    @foreach($this->people as $person)
    @php($pictureUrl = $person->pictureUrl())

    <flux:card size="sm" class="flex h-full flex-col gap-4 p-0 overflow-hidden">
      <div class="aspect-square w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
        @if($pictureUrl)
        <x-picture
          :src="$pictureUrl"
          :alt="$person->name"
          picture-class="block size-full"
          img-class="size-full object-cover"
          onerror="this.classList.add('hidden'); this.closest('picture').nextElementSibling.classList.remove('hidden');" />

        <div class="hidden size-full items-center justify-center">
          <flux:icon name="user" variant="solid" class="size-10 text-zinc-500 dark:text-zinc-400" />
        </div>
        @else
        <div class="flex size-full items-center justify-center">
          <flux:icon name="user" variant="solid" class="size-10 text-zinc-500 dark:text-zinc-400" />
        </div>
        @endif
      </div>

      <div class="flex items-center justify-between gap-3 p-4 pt-0">
        <div class="min-w-0">
          <flux:heading size="sm" class="truncate">{{ $person->name }}</flux:heading>
        </div>

        <div class="flex shrink-0 items-center gap-2">
          <flux:button size="xs" href="{{ route('people.view', $person) }}" wire:navigate>View</flux:button>
        </div>
      </div>
    </flux:card>
    @endforeach
  </div>

  <flux:pagination :paginator="$this->people" class="mt-6" />
  @endif
</div>
