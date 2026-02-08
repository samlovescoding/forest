<?php

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the register page for guests', function (): void {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create an account');
});

it('validates registration input before creating an account', function (): void {
    Livewire::test('pages::register')
        ->set('name', 'A')
        ->set('email', 'invalid-email')
        ->set('password', 'short')
        ->set('password_confirmation', '')
        ->call('submit')
        ->assertHasErrors([
            'name' => 'min',
            'email' => 'email',
            'password' => 'min',
        ])
        ->set('password', 'password123')
        ->set('password_confirmation', 'different-password')
        ->call('submit')
        ->assertHasErrors([
            'password' => 'confirmed',
        ]);

    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

it('requires a unique email address when registering', function (): void {
    $existingUser = User::factory()->create();

    Livewire::test('pages::register')
        ->set('name', 'Ryan Reynolds')
        ->set('email', $existingUser->email)
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('submit')
        ->assertHasErrors([
            'email' => 'unique',
        ]);

    expect(User::query()->count())->toBe(1);
});

it('creates an account and starts email verification for valid registration input', function (): void {
    Mail::fake();

    Livewire::test('pages::register')
        ->set('name', 'Ryan Reynolds')
        ->set('email', 'ryan@forest.test')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('submit')
        ->assertRedirectToRoute('verification');

    $user = User::query()->where('email', 'ryan@forest.test')->first();

    expect($user)->not()->toBeNull();
    expect($user->name)->toBe('Ryan Reynolds');
    expect(Hash::check('password123', $user->password))->toBeTrue();

    $this->assertGuest();
    $this->assertSame($user->id, session('verification_required_for_user'));
    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);
    Mail::assertSent(EmailVerification::class);
});
