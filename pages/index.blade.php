<?php

use Livewire\Component;

new class extends Component
{
  //
};
?>

<div>
  <title>Welcome</title>
  <h1 class="text-4xl">Forest</h1>
  <flux:text>Best Server for Gooners</flux:text>
  <flux:text>Better than Discord or Telegram</flux:text>

  <flux:spacer class="h-16" />

  <div class="flex gap-2">
    <flux:button href="{{ route('login') }}" wire:navigate>
      Login
    </flux:button>
    <flux:button href="{{ route('register') }}" wire:navigate>
      Register
    </flux:button>
  </div>
</div>
