<?php

use App\Livewire\Forms\PersonForm;
use App\Models\Person;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
  use WithFileUploads;

  public PersonForm $form;

  public Person $person;

  public function mount(Person $person): void
  {
    $this->person = $person;
    $this->form->setPerson($person);
  }

  public function submit(): void
  {
    $this->form->update();

    $this->redirect(route('people.view', $this->person));
  }

  public function updated(string $field): void
  {
    if ($field === 'form.name') {
      if ($this->form->full_name === '' || $this->form->full_name === $this->person->full_name) {
        $this->form->full_name = $this->form->name;
        $this->form->slug = str($this->form->name)->slug();
      }
    }
  }
};
?>

<div>
  <title>Edit Person Details</title>
  <flux:heading size="xl" level="1">Edit Person Details</flux:heading>
  <flux:separator variant="subtle" class="mt-4 mb-8" />

  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Display Name"
        wire:model.live.blur="form.name" />

      <flux:input
        label="Full Name"
        wire:model="form.full_name" />

      <flux:input
        label="Slug"
        wire:model="form.slug" disabled />
    </div>



    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="Date of Birth"
        selectable-header
        wire:model="form.birth_date" />

      <flux:date-picker
        label="Date of Death"
        selectable-header clearable
        wire:model="form.death_date" />

      <flux:radio.group wire:model="form.gender" label="Gender" variant="segmented">
        <flux:radio label="Female" value="female" />
        <flux:radio label="Male" value="male" />
        <flux:radio label="Unknown" value="unknown" />
      </flux:radio.group>
    </div>

    <flux:radio.group wire:model="form.sexuality" label="Sexuality" variant="pills">
      <flux:radio label="Straight" value="straight" />
      <flux:tooltip content="Female interested in Females">
        <flux:radio label="Lesbian" value="lesbian" />
      </flux:tooltip>
      <flux:tooltip content="Male interested in Males">
        <flux:radio label="Gay" value="gay" />
      </flux:tooltip>
      <flux:tooltip content="Transitioned from Female to Male">
        <flux:radio label="Trans Male" value="trans-male" />
      </flux:tooltip>
      <flux:tooltip content="Transitioned from Male to Female">
        <flux:radio label="Trans Female" value="trans-female" />
      </flux:tooltip>
      <flux:tooltip content="Male interested in either Males or Females">
        <flux:radio label="Bisexual Male" value="bisexual-male" />
      </flux:tooltip>
      <flux:tooltip content="Female interested in either Males or Females">
        <flux:radio label="Bisexual Female" value="bisexual-female" />
      </flux:tooltip>
      <flux:radio label="Unknown" />
    </flux:radio.group>


    <div class="flex flex-col md:flex-row gap-4 *:w-full">
      <flux:input
        label="Country of Birth"
        placeholder="Country of Birth"
        wire:model="form.birth_country" />
      <flux:input
        label="City of Birth"
        placeholder="City of Birth"
        wire:model="form.birth_city" />
    </div>

    <div class="flex items-start gap-4">
      <div class="relative flex items-center justify-center size-32 rounded transition-colors overflow-hidden
        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15">
        @if($this->form->picture)
        <img src="{{ $this->form->picture->temporaryUrl() }}" class="size-full object-cover" />
        <div class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full font-medium">
          New
        </div>
        @elseif($this->person->pictureUrl())
        <x-picture
          :src="$this->person->pictureUrl()"
          :alt="$this->person->name"
          picture-class="block size-full"
          img-class="size-full object-cover" />
        @else
        <flux:icon name="user" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
        @endif
      </div>

      <flux:file-upload wire:model="form.picture" label="Picture">
        <flux:file-upload.dropzone
          heading="Drop files or click to browse"
          text="Choose a square image: PNG, JPG, GIF"
          with-progress
          inline />
      </flux:file-upload>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <flux:button variant="primary" type="submit">
        Update Person
      </flux:button>

      <flux:button variant="ghost" href="{{ route('people.view', $this->person) }}" wire:navigate>
        Cancel
      </flux:button>
    </div>
  </x-form>
</div>
