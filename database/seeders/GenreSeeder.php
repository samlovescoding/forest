<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->genres() as $genre) {
            Genre::query()->updateOrCreate(
                ['tmdb_id' => $genre['tmdb_id']],
                $genre,
            );
        }
    }

    protected function genres(): array
    {
        return [
            // Movie Genres
            ['name' => 'Action', 'tmdb_id' => 28],
            ['name' => 'Adventure', 'tmdb_id' => 12],
            ['name' => 'Animation', 'tmdb_id' => 16],
            ['name' => 'Comedy', 'tmdb_id' => 35],
            ['name' => 'Crime', 'tmdb_id' => 80],
            ['name' => 'Documentary', 'tmdb_id' => 99],
            ['name' => 'Drama', 'tmdb_id' => 18],
            ['name' => 'Family', 'tmdb_id' => 10751],
            ['name' => 'Fantasy', 'tmdb_id' => 14],
            ['name' => 'History', 'tmdb_id' => 36],
            ['name' => 'Horror', 'tmdb_id' => 27],
            ['name' => 'Music', 'tmdb_id' => 10402],
            ['name' => 'Mystery', 'tmdb_id' => 9648],
            ['name' => 'Romance', 'tmdb_id' => 10749],
            ['name' => 'Science Fiction', 'tmdb_id' => 878],
            ['name' => 'TV Movie', 'tmdb_id' => 10770],
            ['name' => 'Thriller', 'tmdb_id' => 53],
            ['name' => 'War', 'tmdb_id' => 10752],
            ['name' => 'Western', 'tmdb_id' => 37],

            // TV-specific Genres
            ['name' => 'Action & Adventure', 'tmdb_id' => 10759],
            ['name' => 'Kids', 'tmdb_id' => 10762],
            ['name' => 'News', 'tmdb_id' => 10763],
            ['name' => 'Reality', 'tmdb_id' => 10764],
            ['name' => 'Sci-Fi & Fantasy', 'tmdb_id' => 10765],
            ['name' => 'Soap', 'tmdb_id' => 10766],
            ['name' => 'Talk', 'tmdb_id' => 10767],
            ['name' => 'War & Politics', 'tmdb_id' => 10768],
        ];
    }
}
