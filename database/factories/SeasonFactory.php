<?php

namespace Database\Factories;

use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seasonNumber = fake()->numberBetween(1, 10);
        $name = 'Season '.$seasonNumber;

        return [
            'show_id' => Show::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'overview' => fake()->paragraphs(2, true),
            'season_number' => $seasonNumber,
            'episode_count' => fake()->numberBetween(6, 24),
            'air_date' => fake()->dateTimeBetween('-20 years', 'now'),
            'tmdb_id' => fake()->numberBetween(1000, 999999),
            'is_published' => true,
            'is_hidden' => false,
        ];
    }
}
