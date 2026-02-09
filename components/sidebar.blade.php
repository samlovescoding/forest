<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
  #[Computed]
  public function user()
  {
    return Auth::user();
  }

  public function logout()
  {
    Auth::logout();

    return $this->redirectRoute('login');
  }
};
?>


<div class="contents">
  <flux:sidebar collapsible sticky class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.nav>
      <x-sidebar-item icon="home" href="{{ route('home') }}">Home</x-sidebar-item>
      <flux:sidebar.group expandable icon="star" heading="Database" class="grid">
        <x-sidebar-item href="{{ route('people.index') }}" matching="start">People</x-sidebar-item>
        <x-sidebar-item href="{{ route('films.index') }}">Films</x-sidebar-item>
        <x-sidebar-item href="{{ route('shows.index') }}">TV Shows</x-sidebar-item>
      </flux:sidebar.group>
    </flux:sidebar.nav>
    <flux:sidebar.spacer />
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
      <flux:sidebar.profile avatar="{{ $this->user->picture() }}" name="{{ $this->user->name }}" icon:trailing="chevron-up" />
      <x-user-menu />
    </flux:dropdown>
  </flux:sidebar>
  <flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />
    <flux:spacer />
    <flux:dropdown position="top" align="start">
      <flux:profile name="{{ $this->user->name }}" avatar="{{ $this->user->picture() }}" />
      <x-user-menu />
    </flux:dropdown>
  </flux:header>
</div>
