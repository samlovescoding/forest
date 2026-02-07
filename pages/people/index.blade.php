<?php

use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
  use WithPagination;
};
?>

<div>
  <title>People</title>

  <div class="flex justify-between items-center">
    <div>
      <flux:heading size="xl">People</flux:heading>
    </div>
    <div>
      <flux:button size="sm" href="{{ route('people.create') }}" wire:navigate>Add New</flux:button>
    </div>
  </div>
  <flux:separator variant="subtle" class="my-6" />
</div>
