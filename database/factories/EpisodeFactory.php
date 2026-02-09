<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\Show;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucwords(fake()->words(fake()->numberBetween(2, 5), true));

        return [
            'season_id' => Season::factory(),
            'show_id' => Show::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'overview' => fake()->paragraphs(2, true),
            'episode_number' => fake()->numberBetween(1, 24),
            'season_number' => fake()->numberBetween(1, 10),
            'runtime' => fake()->randomElement([22, 30, 42, 45, 60]),
            'air_date' => fake()->dateTimeBetween('-20 years', 'now'),
            'tmdb_id' => fake()->numberBetween(1000, 999999),
            'is_published' => true,
            'is_hidden' => false,
        ];
    }
}
