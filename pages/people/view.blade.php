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
  <title>{{ $this->person->name }} · Person View</title>

  <section class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-emerald-50 via-white to-cyan-50 p-5 shadow-sm sm:p-8 dark:border-white/10 dark:from-emerald-950/30 dark:via-zinc-900 dark:to-cyan-950/30">
    <div class="pointer-events-none absolute -right-14 -top-14 h-40 w-40 rounded-full bg-cyan-300/30 blur-3xl dark:bg-cyan-500/20"></div>
    <div class="pointer-events-none absolute -bottom-16 left-1/3 h-44 w-44 rounded-full bg-emerald-300/30 blur-3xl dark:bg-emerald-500/20"></div>

    <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div class="space-y-2">
        <flux:text class="text-xs font-semibold tracking-[0.22em] text-zinc-500 dark:text-zinc-400">PERSON PROFILE</flux:text>
        <flux:heading level="1" size="xl" class="tracking-tight">{{ $this->person->name }}</flux:heading>
        <flux:text class="text-base text-zinc-600 dark:text-zinc-300">{{ $this->person->full_name }}</flux:text>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <flux:badge size="sm">{{ $this->status }}</flux:badge>
        <flux:badge size="sm">{{ str($this->person->gender)->headline() }}</flux:badge>
        <flux:badge size="sm">{{ str($this->person->sexuality)->headline() }}</flux:badge>
      </div>
    </div>
  </section>

  <div class="flex flex-wrap items-center gap-2">
    <flux:button size="sm" variant="ghost" href="{{ route('people.index') }}" wire:navigate>Back to People</flux:button>
    <flux:button size="sm" variant="primary" href="{{ route('people.edit', $this->person) }}" wire:navigate>Edit Person</flux:button>
  </div>

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[22rem_minmax(0,1fr)]">
    <flux:card class="h-fit space-y-5">
      <div class="relative aspect-square overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 dark:border-white/10 dark:bg-white/10">
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

      <div class="space-y-3">
        <flux:heading size="lg">Quick Snapshot</flux:heading>

        <dl class="space-y-3">
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Age</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->age }} years</dd>
          </div>
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Born</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->birth_date->toFormattedDateString() }}</dd>
          </div>
          <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Status</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->status }}</dd>
          </div>
        </dl>
      </div>
    </flux:card>

    <div class="space-y-6">
      @if($this->person->death_date)
      <flux:callout icon="information-circle" variant="secondary" heading="Recorded as deceased.">
        Date of death: {{ $this->person->death_date->toFormattedDateString() }}.
      </flux:callout>
      @else
      <flux:callout icon="information-circle" variant="secondary" heading="Currently living.">
        No date of death has been recorded.
      </flux:callout>
      @endif

      <flux:card class="space-y-4">
        <flux:heading size="lg">Identity</flux:heading>
        <flux:separator variant="subtle" />

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Display Name</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->name }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Full Name</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->full_name }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Slug</dt>
            <dd class="font-mono text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->slug }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Sexuality</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ str($this->person->sexuality)->headline() }}</dd>
          </div>
        </dl>
      </flux:card>

      <flux:card class="space-y-4">
        <flux:heading size="lg">Life Record</flux:heading>
        <flux:separator variant="subtle" />

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Birth Date</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->birth_date->toFormattedDateString() }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Death Date</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->death_date?->toFormattedDateString() ?? 'N/A' }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Country of Birth</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->birth_country }}</dd>
          </div>

          <div class="space-y-1">
            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">City of Birth</dt>
            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $this->person->birth_city }}</dd>
          </div>
        </dl>
      </flux:card>
    </div>
  </section>
</div>
