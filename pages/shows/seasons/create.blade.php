<?php

use App\Livewire\Forms\SeasonForm;
use App\Models\Show;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public SeasonForm $form;

  public Show $show;

  public function mount(Show $show): void
  {
    $this->show = $show;
    $this->prefill();
  }

  public function prefill(): void
  {
    if (! app()->environment('local')) {
      return;
    }

    $this->form->prefill();
  }

  public function submit(): void
  {
    $season = $this->form->store($this->show);

    $this->redirect(route('seasons.view', [$this->show, $season]));
  }

  public function updated(string $field): void
  {
    if ($field === 'form.name') {
      $this->form->slug = str($this->form->name)->slug();
    }
  }
};
?>

<div>
  <title>Create Season &bull; {{ $this->show->name }}</title>
  <flux:heading size="xl" level="1">Create a new Season for "{{ $this->show->name }}"</flux:heading>
  <flux:separator variant="subtle" class="mt-4 mb-8" />

  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Name"
        wire:model.live.blur="form.name" />

      <flux:input
        label="Slug"
        wire:model="form.slug" />
    </div>

    <flux:textarea
      label="Overview"
      rows="4"
      wire:model="form.overview" />

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Season Number"
        type="number"
        min="0"
        wire:model="form.season_number" />

      <flux:input
        label="Episode Count"
        type="number"
        min="0"
        wire:model="form.episode_count" />
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="Air Date"
        selectable-header
        wire:model="form.air_date" />

      <flux:input
        label="TMDB ID"
        type="number"
        wire:model="form.tmdb_id" />
    </div>

    <div class="flex items-start gap-4">
      <div class="
        relative flex items-center justify-center w-32 aspect-[2/3] rounded transition-colors overflow-hidden
        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15
      ">
        @if($this->form->poster)
        <img src="{{ $this->form->poster->temporaryUrl() }}" class="size-full object-cover" />
        @else
        <flux:icon name="tv" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
        @endif
      </div>

      <flux:file-upload wire:model="form.poster" label="Poster">
        <flux:file-upload.dropzone
          heading="Drop poster or click to browse"
          text="Choose a 2:3 ratio image: PNG, JPG"
          with-progress
          inline />
      </flux:file-upload>
    </div>

    <div class="mt">
      <flux:button variant="primary" type="submit">Create Season</flux:button>
    </div>
  </x-form>
</div>
