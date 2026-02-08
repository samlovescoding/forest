<?php

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows person actions without rendering the gender badge text', function (): void {
  $user = User::factory()->create();

  $person = Person::query()->create([
      'name' => 'Taylor Swift',
      'slug' => 'taylor-swift',
      'full_name' => 'Taylor Alison Swift',
      'birth_date' => '1989-12-13',
      'death_date' => null,
      'gender' => 'female',
      'sexuality' => 'straight',
      'birth_country' => 'United States',
      'birth_city' => 'Reading',
      'picture' => null,
  ]);

  $this->actingAs($user)
      ->get(route('people.index'))
      ->assertSuccessful();
});
