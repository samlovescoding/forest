<?php

use App\Livewire\Forms\FilmForm;
use App\Models\Genre;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public FilmForm $form;

  #[Computed]
  public function genres(): Collection
  {
    return Genre::query()->orderBy('name')->get();
  }

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
    $film = $this->form->store();

    $this->redirect(route('films.view', $film));
  }

  public function updated(string $field): void
  {
    if ($field === 'form.title') {
      $this->form->slug = str($this->form->title)->slug();
    }
  }
};
?>

<div>
  <title>Create Film</title>
  <flux:heading size="xl" level="1">Create a new Film</flux:heading>
  <flux:separator variant="subtle" class="mt-4 mb-8" />

  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Title"
        wire:model.live.blur="form.title" />

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
        label="Runtime (minutes)"
        type="number"
        min="0"
        wire:model="form.runtime" />

      <flux:date-picker
        label="Release Date"
        selectable-header
        wire:model="form.release_date" />
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

    <flux:pillbox wire:model="form.genres" multiple label="Genres" searchable placeholder="Select genres...">
      @foreach($this->genres as $genre)
        <flux:pillbox.option value="{{ $genre->id }}">{{ $genre->name }}</flux:pillbox.option>
      @endforeach
    </flux:pillbox>

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
          <flux:icon name="film" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
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
      <flux:button variant="primary" type="submit">Create Film</flux:button>
    </div>
  </x-form>
</div>
