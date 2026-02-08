<?php

use App\Models\Person;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  public Person $person;

  public function mount(Person $person): void
  {
    $this->person = $person;
  }

  #[Computed]
  public function status(): string
  {
    return $this->person->death_date ? 'Deceased' : 'Living';
  }

  #[Computed]
  public function age(): int
  {
    return $this->person->birth_date->diffInYears($this->person->death_date ?? now());
  }
};
?>

<div class="space-y-6">
  <title>{{ $this->person->name }} &bull; Person View</title>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[22rem_minmax(0,1fr)]">
    <flux:card class="h-fit space-y-5 p-0 overflow-hidden">
      <div class="relative aspect-square border border-zinc-200 bg-zinc-100 dark:border-white/10 dark:bg-white/10">
        @if($this->person->pictureUrl())
        <x-picture
          :src="$this->person->pictureUrl()"
          :alt="$this->person->name"
          picture-class="block size-full"
          img-class="size-full object-cover"
          onerror="this.classList.add('hidden'); this.closest('picture').nextElementSibling.classList.remove('hidden');" />

        <div class="hidden size-full items-center justify-center">
          <flux:avatar size="xl" color="auto" name="{{ $this->person->name }}" />
        </div>
        @else
        <div class="flex size-full items-center justify-center">
          <flux:avatar size="xl" color="auto" name="{{ $this->person->name }}" />
        </div>
        @endif
      </div>

      <div class="space-y-3 p-6 pt-0">
        <div class="flex items-center justify-between gap-2">
          <dt class="text-sm text-zinc-500 dark:text-zinc-400">Name</dt>
          <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->name }}</dd>
        </div>
        <div class="flex items-center justify-between gap-2">
          <dt class="text-sm text-zinc-500 dark:text-zinc-400">Age</dt>
          <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->age }} years</dd>
        </div>
        <div class="flex items-center justify-between gap-2">
          <dt class="text-sm text-zinc-500 dark:text-zinc-400">Born</dt>
          <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->birth_date->toFormattedDateString() }}</dd>
        </div>
        <div class="flex">
          <flux:spacer />
          <flux:button size="sm" href="{{ route('people.edit', $this->person) }}" wire:navigate>Edit</flux:button>
        </div>
      </div>
    </flux:card>

    <div>
      Blank Page
    </div>
  </section>
</div>
