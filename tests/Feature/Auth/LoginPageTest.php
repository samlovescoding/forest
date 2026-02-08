<?php

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the login page for guests', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Welcome back');
});

it('validates email and password input before attempting login', function (): void {
    Livewire::test('pages::login')
        ->set('email', 'invalid-email')
        ->set('password', 'short')
        ->call('submit')
        ->assertHasErrors([
            'email' => 'email',
            'password' => 'min',
        ]);

    $this->assertGuest();
});

it('shows an email error when the account does not exist', function (): void {
    Livewire::test('pages::login')
        ->set('email', 'missing@forest.test')
        ->set('password', 'password123')
        ->call('submit')
        ->assertHasErrors([
            'email' => "Email isn't associated with any account.",
        ]);

    $this->assertGuest();
});

it('shows a password error when the password is incorrect', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    Livewire::test('pages::login')
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('submit')
        ->assertHasErrors([
            'password' => 'Your password is incorrect.',
        ]);

    $this->assertGuest();
});

it('redirects unverified users to verification and queues a verification email', function (): void {
    Mail::fake();

    $user = User::factory()->unverified()->create([
        'password' => Hash::make('password123'),
    ]);

    Livewire::test('pages::login')
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->call('submit')
        ->assertRedirectToRoute('verification');

    $this->assertGuest();
    $this->assertSame($user->id, session('verification_required_for_user'));
    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);
    Mail::assertSent(EmailVerification::class);
});

it('logs in verified users and redirects them home', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    Livewire::test('pages::login')
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->call('submit')
        ->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);
});
