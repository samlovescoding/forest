<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Show>
 */
class ShowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucwords(fake()->words(fake()->numberBetween(1, 4), true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'overview' => fake()->paragraphs(2, true),
            'episode_run_time' => fake()->randomElement([22, 30, 42, 45, 60]),
            'number_of_seasons' => fake()->numberBetween(1, 12),
            'number_of_episodes' => fake()->numberBetween(8, 200),
            'first_air_date' => fake()->dateTimeBetween('-30 years', '-1 year'),
            'tmdb_id' => fake()->numberBetween(1000, 999999),
            'is_published' => true,
            'is_hidden' => false,
        ];
    }
}
