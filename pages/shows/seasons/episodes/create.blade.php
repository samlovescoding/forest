<?php

use App\Livewire\Forms\EpisodeForm;
use App\Models\Season;
use App\Models\Show;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public EpisodeForm $form;

  public Show $show;

  public Season $season;

  public function mount(Show $show, Season $season): void
  {
    $this->show = $show;
    $this->season = $season;
    $this->form->season_number = $season->season_number;
    $this->prefill();
  }

  public function prefill(): void
  {
    if (! app()->environment('local')) {
      return;
    }

    $this->form->prefill();
    $this->form->season_number = $this->season->season_number;
  }

  public function submit(): void
  {
    $episode = $this->form->store($this->season);

    $this->redirect(route('episodes.view', [$this->show, $this->season, $episode]));
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
  <title>Create Episode &bull; {{ $this->season->name }}</title>
  <flux:heading size="xl" level="1">Create a new Episode for "{{ $this->season->name }}"</flux:heading>
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
        label="Episode Number"
        type="number"
        min="1"
        wire:model="form.episode_number" />

      <flux:input
        label="Season Number"
        type="number"
        min="0"
        wire:model="form.season_number" />

      <flux:input
        label="Runtime (minutes)"
        type="number"
        min="0"
        wire:model="form.runtime" />
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="Air Date"
        selectable-header
        wire:model="form.air_date" />

      <flux:input
        label="Production Code"
        wire:model="form.production_code" />
    </div>

    <flux:input
      label="TMDB ID"
      type="number"
      wire:model="form.tmdb_id" />

    <div class="flex items-start gap-4">
      <div class="
        relative flex items-center justify-center w-48 aspect-video rounded transition-colors overflow-hidden
        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15
      ">
        @if($this->form->still)
        <img src="{{ $this->form->still->temporaryUrl() }}" class="size-full object-cover" />
        @else
        <flux:icon name="film" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
        @endif
      </div>

      <flux:file-upload wire:model="form.still" label="Still Image">
        <flux:file-upload.dropzone
          heading="Drop still image or click to browse"
          text="Choose a 16:9 ratio image: PNG, JPG"
          with-progress
          inline />
      </flux:file-upload>
    </div>

    <div class="mt">
      <flux:button variant="primary" type="submit">Create Episode</flux:button>
    </div>
  </x-form>
</div>
