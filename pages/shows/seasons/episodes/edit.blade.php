<?php

use App\Livewire\Forms\EpisodeForm;
use App\Models\Episode;
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

  public Episode $episode;

  public function mount(Show $show, Season $season, Episode $episode): void
  {
    $this->show = $show;
    $this->season = $season;
    $this->episode = $episode;
    $this->form->setEpisode($episode);
  }

  public function submit(): void
  {
    $this->form->update();

    $this->redirect(route('episodes.view', [$this->show, $this->season, $this->episode]));
  }

  public function publish(): void
  {
    $this->episode->update([
        'is_published' => true,
    ]);
  }

  public function toggleVisibility(): void
  {
    $this->episode->update([
        'is_hidden' => ! $this->episode->is_hidden,
    ]);
  }
};
?>

<div>
  <title>Editing {{ $this->form->name }}</title>
  <flux:heading size="xl" level="1">Editing "{{ $this->form->name }}"</flux:heading>
  <flux:separator variant="subtle" class="mt-4 mb-8"/>

  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Name"
        wire:model.live.blur="form.name"/>

      <flux:input
        label="Slug"
        wire:model="form.slug" disabled/>
    </div>

    <flux:textarea
      label="Overview"
      rows="4"
      wire:model="form.overview"/>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Episode Number"
        type="number"
        min="1"
        wire:model="form.episode_number"/>

      <flux:input
        label="Season Number"
        type="number"
        min="0"
        wire:model="form.season_number"/>

      <flux:input
        label="Runtime (minutes)"
        type="number"
        min="0"
        wire:model="form.runtime"/>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="Air Date"
        selectable-header
        wire:model="form.air_date"/>

      <flux:input
        label="Production Code"
        wire:model="form.production_code"/>
    </div>

    <flux:input
      label="TMDB ID"
      type="number"
      wire:model="form.tmdb_id"/>

    <div class="flex items-start gap-4">
      <div class="relative flex items-center justify-center w-48 aspect-video rounded transition-colors overflow-hidden
        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15">
        @if($this->form->still)
          <img src="{{ $this->form->still->temporaryUrl() }}" class="size-full object-cover"
               alt="Newly uploaded still"/>
          <div
            class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full font-medium">
            New
          </div>
        @else
          <x-picture
            :src="$this->episode->stillUrl(...)"
            :alt="$this->episode->name"
            icon="film"
          />
        @endif
      </div>

      <flux:file-upload wire:model="form.still" label="Still Image">
        <flux:file-upload.dropzone
          heading="Drop still image or click to browse"
          text="Choose a 16:9 ratio image: PNG, JPG"
          with-progress
          inline/>
      </flux:file-upload>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <flux:button variant="primary" type="submit">
        Update
      </flux:button>

      <x-button-back/>

      <flux:spacer/>

      @unless($this->episode->is_published)
        <flux:button
          wire:confirm="Are you sure you want to publish this episode? This cannot be reverted."
          variant="ghost"
          wire:click="publish">
          Publish
        </flux:button>
      @else
        <flux:button
          wire:confirm="Are you sure?"
          tooltip="This hides from listing pages and searches."
          variant="ghost"
          wire:click="toggleVisibility">
          @if($this->episode->is_hidden)
            Unhide
          @else
            Hide
          @endif
        </flux:button>
      @endunless
    </div>
  </x-form>
</div>
