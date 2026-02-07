<?php

use App\Livewire\Forms\PersonForm;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {

  use WithFileUploads;

  public PersonForm $form;

  public $picture;

  public function submit()
  {
    $fields = $this->form->all();

    dd($fields);
  }
};
?>

<div>
  <x-form class="flex flex-col gap-4" wire:submit.prevent="submit">

    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="Display Name"
        wire:model="form.name" />

      <flux:input
        label="Name"
        wire:model="form.stage_name" />

      <flux:input
        label="Full Name"
        wire:model="form.full_name" />
    </div>



    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:date-picker
        label="Date of Birth"
        wire:model="form.birth_date" />

      <flux:date-picker
        label="Date of Death"
        wire:model="form.death_date" />

      <flux:radio.group wire:model="form.gender" label="Gender" variant="segmented">
        <flux:radio label="Female" />
        <flux:radio label="Male" />
        <flux:radio label="Unknown" />
      </flux:radio.group>
    </div>

    <flux:radio.group wire:model="form.sexuality" label="Sexuality" variant="segmented">
      <flux:radio label="Straight" />
      <flux:radio label="Lesbian" />
      <flux:radio label="Gay" />
      <flux:radio label="Trans Male" tooltip="Gay" />
      <flux:radio label="Trans Female" />
      <flux:radio label="Unknown" />
    </flux:radio.group>


    <div class="flex flex-col lg:flex-row gap-4 *:w-full">
      <flux:input
        label="City of Birth"
        placeholder="City of Birth"
        wire:model="form.birth_city" />

      <flux:input
        label="Country of Birth"
        placeholder="Country of Birth"
        wire:model="form.birth_country" />
    </div>

    <div class="flex items-end gap-4">
      <div class="
        relative flex items-center justify-center size-26 rounded transition-colors
        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15
      ">
        <!-- <img src="https://fluxui.dev/img/demo/user.png" class="size-full object-cover rounded-full" /> -->
        <flux:icon name="user" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
      </div>

      <flux:file-upload wire:model="form.picture" label="Picture">
        <flux:file-upload.dropzone
          heading="Drop files or click to browse"
          text="Choose a square image: PNG, JPG, GIF"
          with-progress
          inline />
      </flux:file-upload>
    </div>
    @dump($this->form->picture)


    <div class="mt">
      <flux:button variant="primary" type="submit">Create</flux:button>
    </div>
  </x-form>
</div>
