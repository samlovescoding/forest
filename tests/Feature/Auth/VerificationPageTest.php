<?php

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects to login when verification user is missing from session', function (): void {
    $this->get(route('verification'))
        ->assertRedirectToRoute('login');
});

it('renders the verification page when verification user is in session', function (): void {
    $user = User::factory()->unverified()->create();

    $this->withSession([
        'verification_required_for_user' => $user->id,
    ])->get(route('verification'))
        ->assertOk()
        ->assertSee($user->email);
});

it('resends a verification email for the session user', function (): void {
    Mail::fake();

    $user = User::factory()->unverified()->create();
    session()->put('verification_required_for_user', $user->id);

    Livewire::test('pages::verification')
        ->call('resendEmail');

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);
    Mail::assertSent(EmailVerification::class);
});

it('validates the verification code format before attempting verification', function (): void {
    $user = User::factory()->unverified()->create();
    session()->put('verification_required_for_user', $user->id);

    Livewire::test('pages::verification')
        ->set('code', '')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'required',
        ])
        ->set('code', '123')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'size',
        ]);

    $this->assertGuest();
});

it('shows an error when the verification code is expired', function (): void {
    $user = User::factory()->unverified()->create();
    session()->put('verification_required_for_user', $user->id);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '123456',
        'created_at' => now()->subMinutes(16),
    ]);

    Livewire::test('pages::verification')
        ->set('code', '123456')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'Code is expired or incorrect.',
        ]);

    $this->assertGuest();
    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('shows an error when the verification code is incorrect', function (): void {
    $user = User::factory()->unverified()->create();
    session()->put('verification_required_for_user', $user->id);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '654321',
        'created_at' => now(),
    ]);

    Livewire::test('pages::verification')
        ->set('code', '123456')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'Code is expired or incorrect.',
        ]);

    $this->assertGuest();
    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('verifies the email, logs in the user, and redirects home with a valid code', function (): void {
    $user = User::factory()->unverified()->create();
    session()->put('verification_required_for_user', $user->id);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '123456',
        'created_at' => now(),
    ]);

    Livewire::test('pages::verification')
        ->set('code', '123456')
        ->call('submit')
        ->assertRedirectToRoute('home');

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->email_verified_at)->not()->toBeNull();
    expect(session()->has('verification_required_for_user'))->toBeFalse();
});
