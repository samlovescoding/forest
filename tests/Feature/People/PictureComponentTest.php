<?php

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('renders avif and webp sources for non gif images', function (): void {
    $this->blade('<x-picture src="/storage/people/jane.jpg?v=1" alt="Jane Doe" picture-class="block size-full" img-class="size-full object-cover" />')
        ->assertSee('type="image/avif"', false)
        ->assertSee('type="image/webp"', false)
        ->assertSee('srcset="/storage/people/jane.avif?v=1"', false)
        ->assertSee('srcset="/storage/people/jane.webp?v=1"', false)
        ->assertSee('src="/storage/people/jane.jpg?v=1"', false);
});

it('renders only fallback img for gif images', function (): void {
    $this->blade('<x-picture src="/storage/people/animated.gif" alt="Animated Person" picture-class="block size-full" img-class="size-full object-cover" />')
        ->assertDontSee('type="image/avif"', false)
        ->assertDontSee('type="image/webp"', false)
        ->assertSee('src="/storage/people/animated.gif"', false);
});

it('renders people view page with picture sources when a person has an image', function (): void {
    $user = User::factory()->create();
    $person = pictureComponentCreatePerson([
        'name' => 'Taylor Swift',
        'slug' => 'taylor-swift',
        'picture' => 'people/taylor-swift.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('people.view', $person))
        ->assertSuccessful()
        ->assertSee('type="image/avif"', false)
        ->assertSee('type="image/webp"', false)
        ->assertSee('srcset="'.Storage::url('people/taylor-swift.avif').'"', false)
        ->assertSee('srcset="'.Storage::url('people/taylor-swift.webp').'"', false)
        ->assertSee('src="'.Storage::url('people/taylor-swift.jpg').'"', false);
});

function pictureComponentCreatePerson(array $overrides = []): Person
{
    return Person::query()->create(array_merge([
        'name' => 'Jane Doe',
        'slug' => 'jane-doe',
        'full_name' => 'Jane Marie Doe',
        'birth_date' => '1990-01-01',
        'death_date' => null,
        'gender' => 'female',
        'sexuality' => 'straight',
        'birth_country' => 'United States of America',
        'birth_city' => 'New York',
        'picture' => null,
    ], $overrides));
}
