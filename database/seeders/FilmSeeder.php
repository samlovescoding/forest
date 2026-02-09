<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FilmSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->films() as $film) {
            $this->seedFilm($film);
        }
    }

    protected function seedFilm(array $attributes): void
    {
        Film::query()->updateOrCreate(
            ['title' => $attributes['title']],
            $attributes,
        );
    }

    protected function films(): array
    {
        return [
            $this->film(
                title: 'The Dark Knight',
                overview: 'Batman raises the stakes in his war on crime. With the help of Lt. Jim Gordon and District Attorney Harvey Dent, Batman sets out to dismantle the remaining criminal organizations that plague the streets.',
                runtime: 152,
                releaseDate: '2008-07-18',
                tmdbId: 155,
                imdbId: 'tt0468569',
            ),
            $this->film(
                title: 'Inception',
                overview: 'Cobb, a skilled thief who commits corporate espionage by infiltrating the subconscious of his targets is offered a chance to regain his old life as payment for a task considered to be impossible.',
                runtime: 148,
                releaseDate: '2010-07-16',
                tmdbId: 27205,
                imdbId: 'tt1375666',
            ),
            $this->film(
                title: 'Interstellar',
                overview: 'The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel and conquer the vast distances involved in an interstellar voyage.',
                runtime: 169,
                releaseDate: '2014-11-07',
                tmdbId: 157336,
                imdbId: 'tt0816692',
            ),
            $this->film(
                title: 'Dune',
                overview: 'Paul Atreides, a brilliant and gifted young man born into a great destiny beyond his understanding, must travel to the most dangerous planet in the universe to ensure the future of his family and his people.',
                runtime: 155,
                releaseDate: '2021-10-22',
                tmdbId: 438631,
                imdbId: 'tt1160419',
            ),
            $this->film(
                title: 'Oppenheimer',
                overview: 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
                runtime: 180,
                releaseDate: '2023-07-21',
                tmdbId: 872585,
                imdbId: 'tt15398776',
            ),
            $this->film(
                title: 'Barbie',
                overview: 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land. However, when they get a chance to go to the real world, they soon discover the joys and perils of living among humans.',
                runtime: 114,
                releaseDate: '2023-07-21',
                tmdbId: 346698,
                imdbId: 'tt1517268',
            ),
            $this->film(
                title: 'Spider-Man: Across the Spider-Verse',
                overview: 'After reuniting with Gwen Stacy, Brooklyn\'s full-time, friendly neighborhood Spider-Man is catapulted across the Multiverse, where he encounters the Spider Society.',
                runtime: 140,
                releaseDate: '2023-06-02',
                tmdbId: 569094,
                imdbId: 'tt9362722',
            ),
            $this->film(
                title: 'Everything Everywhere All at Once',
                overview: 'An aging Chinese immigrant is swept up in an insane adventure, where she alone can save what\'s important to her by connecting with the lives she could have led in other universes.',
                runtime: 139,
                releaseDate: '2022-03-25',
                tmdbId: 545611,
                imdbId: 'tt6710474',
            ),
            $this->film(
                title: 'The Batman',
                overview: 'In his second year of fighting crime, Batman uncovers corruption in Gotham City that connects to his own family while facing a serial killer known as the Riddler.',
                runtime: 176,
                releaseDate: '2022-03-04',
                tmdbId: 414906,
                imdbId: 'tt1877830',
            ),
            $this->film(
                title: 'Top Gun: Maverick',
                overview: 'After more than thirty years of service as one of the Navy\'s top aviators, Pete Mitchell is where he belongs, pushing the envelope as a courageous test pilot and dodging the advancement in rank that would ground him.',
                runtime: 130,
                releaseDate: '2022-05-27',
                tmdbId: 361743,
                imdbId: 'tt1745960',
            ),
        ];
    }

    protected function film(
        string $title,
        string $overview,
        int $runtime,
        string $releaseDate,
        int $tmdbId,
        string $imdbId,
    ): array {
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'overview' => $overview,
            'runtime' => $runtime,
            'release_date' => $releaseDate,
            'tmdb_id' => $tmdbId,
            'imdb_id' => $imdbId,
            'is_published' => true,
            'is_hidden' => false,
        ];
    }
}
