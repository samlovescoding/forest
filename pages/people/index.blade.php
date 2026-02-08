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
        ->latest('id')
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

    <flux:button size="sm" href="{{ route('people.create') }}" wire:navigate>Enroll</flux:button>
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

    <flux:card size="sm" class="flex h-full flex-col gap-4">
      <div class="aspect-square w-full overflow-hidden rounded-lg border border-zinc-200 bg-zinc-100 dark:border-white/10 dark:bg-white/10">
        @if($pictureUrl)
        <img
          src="{{ $pictureUrl }}"
          alt="{{ $person->name }}"
          class="size-full object-cover"
          onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');" />

        <div class="hidden size-full items-center justify-center">
          <flux:icon name="user" variant="solid" class="size-10 text-zinc-500 dark:text-zinc-400" />
        </div>
        @else
        <div class="flex size-full items-center justify-center">
          <flux:icon name="user" variant="solid" class="size-10 text-zinc-500 dark:text-zinc-400" />
        </div>
        @endif
      </div>

      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <flux:heading size="sm" class="truncate">{{ $person->name }}</flux:heading>
        </div>

        <flux:badge size="sm">{{ str($person->gender)->headline() }}</flux:badge>
      </div>

      <div class="mt-auto flex items-center gap-2">
        <flux:button size="xs" variant="ghost" href="{{ route('people.view', $person) }}" wire:navigate>View</flux:button>
        <flux:button size="xs" variant="primary" href="{{ route('people.edit', $person) }}" wire:navigate>Edit</flux:button>
      </div>
    </flux:card>
    @endforeach
  </div>

  <flux:pagination :paginator="$this->people" class="mt-6" />
  @endif
</div>
