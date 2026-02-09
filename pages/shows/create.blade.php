<?php

use App\Livewire\Forms\ShowForm;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public ShowForm $form;

  public function mount(): void
  {
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
    $show = $this->form->store();

    $this->redirect(route('shows.view', $show));
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
  <title>Create Show</title>
  <flux:heading size="xl" level="1">Create a new Show</flux:heading>
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
        label="Episode Run Time (minutes)"
        type="number"
        min="0"
        wire:model="form.episode_run_time" />

      <flux:input
        label="Number of Seasons"
        type="number"
        min="0"
        wire:model="form.number_of_seasons" />

      <flux:input
        label="Number of Episodes"
        type="number"
        min="0"
        wire:model="form.number_of_episodes" />
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="First Air Date"
        selectable-header
        wire:model="form.first_air_date" />

      <flux:date-picker
        label="Last Air Date"
        selectable-header
        clearable
        wire:model="form.last_air_date" />
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="TMDB ID"
        type="number"
        wire:model="form.tmdb_id" />

      <flux:input
        label="IMDB ID"
        placeholder="tt1234567"
        wire:model="form.imdb_id" />
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
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

      <div class="flex items-start gap-4">
        <div class="
          relative flex items-center justify-center w-48 aspect-video rounded transition-colors overflow-hidden
          border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
          bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15
        ">
          @if($this->form->backdrop)
          <img src="{{ $this->form->backdrop->temporaryUrl() }}" class="size-full object-cover" />
          @else
          <flux:icon name="photo" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
          @endif
        </div>

        <flux:file-upload wire:model="form.backdrop" label="Backdrop">
          <flux:file-upload.dropzone
            heading="Drop backdrop or click to browse"
            text="Choose a 16:9 ratio image: PNG, JPG"
            with-progress
            inline />
        </flux:file-upload>
      </div>
    </div>

    <div class="mt">
      <flux:button variant="primary" type="submit">Create Show</flux:button>
    </div>
  </x-form>
</div>
