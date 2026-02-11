<?php

use App\Livewire\Forms\AppearanceForm;
use App\Models\Episode;
use App\Models\Film;
use App\Models\Person;
use App\Models\Season;
use App\Models\Show;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  public AppearanceForm $form;

  public string $personQuery = '';

  public string $filmQuery = '';

  public string $showQuery = '';

  public function updated(string $field): void
  {
    if ($field === 'form.title') {
      if ($this->form->slug === '') {
        $this->form->slug = str($this->form->title)->slug();
      }
    }
  }

  #[Computed]
  public function people()
  {
    return Person::select(['id', 'name'])->when($this->personQuery != '',
      function ($query) {
        $query->where('name', 'like', '%'.$this->personQuery.'%');
      })
        ->where('is_published', '=', true)
        ->where('is_hidden', '=', false)
        ->orderBy('name')
        ->limit(10)
        ->get();
  }

  #[Computed]
  public function films()
  {
    return Film::select(['id', 'title'])->when($this->filmQuery != '',
      function ($query) {
        $query->where('title', 'like', '%'.$this->filmQuery.'%');
      })
        ->where('is_published', '=', true)
        ->where('is_hidden', '=', false)
        ->orderBy('title')
        ->limit(10)
        ->get();
  }

  #[Computed]
  public function shows()
  {
    return Show::select(['id', 'name'])->when($this->showQuery != '',
      function ($query) {
        $query->where('name', 'like', '%'.$this->showQuery.'%');
      })
        ->where('is_published', '=', true)
        ->where('is_hidden', '=', false)
        ->orderBy('name')
        ->limit(10)
        ->get();
  }

  #[Computed]
  public function seasons()
  {
    if (! isset($this->form->show_id)) {
      return [];
    }

    return Season::select(['id', 'name', 'season_number'])
        ->where('show_id', $this->form->show_id)
        ->where('is_published', '=', true)
        ->where('is_hidden', '=', false)
        ->orderBy('season_number')
        ->get();
  }

  #[Computed]
  public function episodes()
  {
    if (! isset($this->form->show_id)) {
      return [];
    }

    return Episode::select(['id', 'name', 'episode_number'])
        ->where('show_id', $this->form->show_id)
        ->where('is_published', '=', true)
        ->where('is_hidden', '=', false)
        ->orderBy('episode_number')
        ->get();
  }
};
?>


<div>
  <title>Upload appearance</title>
  <flux:heading size="xl" level="1">Upload an Appearance</flux:heading>
  <flux:separator variant="subtle" class="mt-4 mb-8"/>

  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex *:w-full gap-2">
      <flux:select label="Person" wire:model="form.person_id" clearable variant="combobox" :filter="false">
        <x-slot name="input">
          <flux:select.input wire:model.live="personQuery" placeholder="Search a person"/>
        </x-slot>
        @foreach($this->people as $person)
          <flux:select.option value="{{ $person->id }}">
            {{ $person->name }}
          </flux:select.option>
        @endforeach
      </flux:select>
      @if(!isset($this->form->show_id))
        <flux:select label="Film" wire:model="form.film_id" clearable variant="combobox" :filter="false">
          <x-slot name="input">
            <flux:select.input wire:model.live="filmQuery" placeholder="Search a film" clearable/>
          </x-slot>
          @foreach($this->films as $film)
            <flux:select.option value="{{ $film->id }}">
              {{ $film->title }}
            </flux:select.option>
          @endforeach
        </flux:select>
      @endif
      @if(!isset($this->form->film_id))
        <flux:select label="Show" wire:model.live="form.show_id" clearable variant="combobox" :filter="false">
          <x-slot name="input">
            <flux:select.input wire:model.live="showQuery" placeholder="Search a show" clearable/>
          </x-slot>
          @foreach($this->shows as $show)
            <flux:select.option value="{{ $show->id }}">
              {{ $show->name }}
            </flux:select.option>
          @endforeach
        </flux:select>
      @endif
      @isset($this->form->show_id)
        <flux:select label="Season" wire:model.live="form.season_id" variant="combobox"
                     :filter="false" clearable placeholder="Pick a season">
          @foreach($this->seasons as $season)
            <flux:select.option value="{{ $season->id }}">
              {{ $season->season_number }}. {{ $season->name }}
            </flux:select.option>
          @endforeach
        </flux:select>
        @isset($this->form->season_id)
          <flux:select label="Episode" wire:model.live="form.episode_id" variant="combobox"
                       :filter="false" clearable placeholder="Pick an episode">
            @foreach($this->episodes as $episode)
              <flux:select.option value="{{ $episode->id }}">
                {{ $episode->episode_number }}. {{ $episode->name }}
              </flux:select.option>
            @endforeach
          </flux:select>
        @endisset
      @endisset
    </div>

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Title"
        wire:model.live.blur="form.title"/>
      <flux:input
        label="Slug"
        wire:model="form.slug"/>
    </div>

    <div class="mt-16">
      <flux:button variant="primary" type="submit">Upload</flux:button>
    </div>
  </x-form>
</div>
