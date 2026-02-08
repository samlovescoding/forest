<?php

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the recovery page for guests', function (): void {
    $this->get(route('recovery'))
        ->assertOk()
        ->assertSee('Account Recovery');
});

it('validates email format before sending a recovery code', function (): void {
    Livewire::test('pages::recovery')
        ->set('email', 'invalid-email')
        ->call('sendEmail')
        ->assertHasErrors([
            'email' => 'email',
        ]);
});

it('shows an email error when recovery email is not registered', function (): void {
    Livewire::test('pages::recovery')
        ->set('email', 'missing@forest.test')
        ->call('sendEmail')
        ->assertHasErrors([
            'email' => 'Email is not registered to any account.',
        ]);
});

it('sends a recovery code for registered users and moves to code entry step', function (): void {
    Mail::fake();

    $user = User::factory()->create();

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->call('sendEmail')
        ->assertSet('isCodeSent', true);

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);
    Mail::assertSent(EmailVerification::class);
});

it('validates email format when resending a recovery code', function (): void {
    Livewire::test('pages::recovery')
        ->set('email', 'invalid-email')
        ->call('resendEmail')
        ->assertHasErrors([
            'email' => 'email',
        ]);
});

it('resends a recovery code for registered users', function (): void {
    Mail::fake();

    $user = User::factory()->create();

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '111111',
        'created_at' => now()->subMinutes(3),
    ]);

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->call('resendEmail');

    Mail::assertSent(EmailVerification::class);

    $latestToken = DB::table('password_reset_tokens')
        ->where('email', $user->email)
        ->latest()
        ->first();

    expect($latestToken)->not->toBeNull();
    expect($latestToken->token)->toHaveLength(6);
    expect($latestToken->token)->not->toBe('111111');
});

it('validates required recovery fields before changing password', function (): void {
    $user = User::factory()->create();

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->set('code', '123')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'different-password')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'size',
            'password' => 'confirmed',
        ]);
});

it('shows a code error when recovery token is missing or expired', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '123456',
        'created_at' => now()->subMinutes(16),
    ]);

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->set('code', '123456')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'Code is expired or incorrect.',
        ]);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('shows a code error when recovery token does not match', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '999999',
        'created_at' => now(),
    ]);

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->set('code', '123456')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('submit')
        ->assertHasErrors([
            'code' => 'Code is expired or incorrect.',
        ]);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('changes the password and redirects to login when recovery code is valid', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => '654321',
        'created_at' => now(),
    ]);

    Livewire::test('pages::recovery')
        ->set('email', $user->email)
        ->set('code', '654321')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('submit')
        ->assertRedirectToRoute('login');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeFalse();
    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
