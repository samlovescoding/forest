<?php

use App\Livewire\Forms\ShowForm;
use App\Models\Genre;
use App\Models\Show;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public ShowForm $form;

  public Show $show;

  #[Computed]
  public function genres(): Collection
  {
    return Genre::query()->orderBy('name')->get();
  }

  public function mount(Show $show): void
  {
    $this->show = $show;
    $this->form->setShow($show);
  }

  public function submit(): void
  {
    $this->form->update();

    $this->redirect(route('shows.view', $this->show));
  }

  public function publish(): void
  {
    $this->show->update([
        'is_published' => true,
    ]);
  }

  public function toggleVisibility(): void
  {
    $this->show->update([
        'is_hidden' => ! $this->show->is_hidden,
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
        label="Episode Run Time (minutes)"
        type="number"
        min="0"
        wire:model="form.episode_run_time"/>

      <flux:input
        label="Number of Seasons"
        type="number"
        min="0"
        wire:model="form.number_of_seasons"/>

      <flux:input
        label="Number of Episodes"
        type="number"
        min="0"
        wire:model="form.number_of_episodes"/>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="First Air Date"
        selectable-header
        wire:model="form.first_air_date"/>

      <flux:date-picker
        label="Last Air Date"
        selectable-header
        clearable
        wire:model="form.last_air_date"/>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="TMDB ID"
        type="number"
        wire:model="form.tmdb_id"/>

      <flux:input
        label="IMDB ID"
        placeholder="tt1234567"
        wire:model="form.imdb_id"/>
    </div>

    <flux:pillbox wire:model="form.genres" multiple label="Genres" searchable placeholder="Select genres...">
      @foreach($this->genres as $genre)
        <flux:pillbox.option value="{{ $genre->id }}">{{ $genre->name }}</flux:pillbox.option>
      @endforeach
    </flux:pillbox>

    <div class="flex flex-col lg:flex-row gap-8">
      <div class="flex items-start gap-4">
        <div class="relative flex items-center justify-center w-32 aspect-[2/3] rounded transition-colors overflow-hidden
          border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
          bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15">
          @if($this->form->poster)
            <img src="{{ $this->form->poster->temporaryUrl() }}" class="size-full object-cover"
                 alt="Newly uploaded poster"/>
            <div
              class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full font-medium">
              New
            </div>
          @elseif($this->show->poster_path)
            <img src="{{ Storage::disk('public')->url($this->show->poster_path) }}"
                 alt="{{ $this->show->name }}"
                 class="size-full object-cover"/>
          @else
            <flux:icon name="tv" variant="solid" class="text-zinc-500 dark:text-zinc-400"/>
          @endif
        </div>

        <flux:file-upload wire:model="form.poster" label="Poster">
          <flux:file-upload.dropzone
            heading="Drop poster or click to browse"
            text="Choose a 2:3 ratio image: PNG, JPG"
            with-progress
            inline/>
        </flux:file-upload>
      </div>

      <div class="flex items-start gap-4">
        <div class="relative flex items-center justify-center w-48 aspect-video rounded transition-colors overflow-hidden
          border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
          bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15">
          @if($this->form->backdrop)
            <img src="{{ $this->form->backdrop->temporaryUrl() }}" class="size-full object-cover"
                 alt="Newly uploaded backdrop"/>
            <div
              class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full font-medium">
              New
            </div>
          @elseif($this->show->backdrop_path)
            <img src="{{ Storage::disk('public')->url($this->show->backdrop_path) }}"
                 alt="{{ $this->show->name }} backdrop"
                 class="size-full object-cover"/>
          @else
            <flux:icon name="photo" variant="solid" class="text-zinc-500 dark:text-zinc-400"/>
          @endif
        </div>

        <flux:file-upload wire:model="form.backdrop" label="Backdrop">
          <flux:file-upload.dropzone
            heading="Drop backdrop or click to browse"
            text="Choose a 16:9 ratio image: PNG, JPG"
            with-progress
            inline/>
        </flux:file-upload>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <flux:button variant="primary" type="submit">
        Update
      </flux:button>

      <x-button-back/>

      <flux:spacer/>

      @unless($this->show->is_published)
        <flux:button
          wire:confirm="Are you sure you want to publish this show? This cannot be reverted."
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
          @if($this->show->is_hidden)
            Unhide
          @else
            Hide
          @endif
        </flux:button>
      @endunless
    </div>
  </x-form>
</div>
