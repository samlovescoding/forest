<?php

use App\Mocks\WithLoginMock;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::authentication')] class extends Component
{
  use WithLoginMock;

  #[Validate('email')]
  public string $email;

  #[Validate('min:8')]
  public string $password;

  public function mount()
  {
    if (app()->environment('local')) {
      $this->prefill();
    }
  }

  public function submit()
  {
    $fields = $this->validate();

    $user = User::where('email', $fields['email'])->first();

    if (! $user) {
      return $this->addError('email', "Email isn't associated with any account.");
    }

    $isPasswordCorrect = password_verify($fields['password'], $user->password);

    if (! $isPasswordCorrect) {
      return $this->addError('password', 'Your password is incorrect.');
    }

    if ($user->email_verified_at === null) {
      Session::put('verification_required_for_user', $user->id);
      $user->sendEmailVerification();

      return $this->redirectRoute('verification');
    }

    Auth::login($user);

    return $this->redirectRoute('home');
  }
};
?>

<div class="w-80 max-w-80 space-y-6">
  <title>Log in to your account</title>
  <flux:heading class="text-center" size="xl">Welcome back</flux:heading>


  <form method="POST" wire:submit="submit" class="flex flex-col gap-6">
    <flux:input wire:model="email" label="Email" type="email" placeholder="ryanreynolds@gmail.com" />

    <flux:field>
      <div class="mb-3 flex justify-between">
        <flux:label>Password</flux:label>

        <flux:link href="{{ route('recovery') }}" variant="subtle" class="text-sm">
          Forgot password?
        </flux:link>
      </div>

      <flux:input wire:model="password" type="password" placeholder="Your password" />

      <flux:error name="password" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full">Log in</flux:button>
  </form>

  <flux:subheading class="text-center">
    First time around here? <flux:link wire:navigate href="{{ route('register') }}">Sign up for free</flux:link>
  </flux:subheading>
</div>
