<?php

namespace Database\Seeders;

use App\Models\Show;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShowSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->shows() as $show) {
            $this->seedShow($show);
        }
    }

    protected function seedShow(array $attributes): void
    {
        Show::query()->updateOrCreate(
            ['name' => $attributes['name']],
            $attributes,
        );
    }

    protected function shows(): array
    {
        return [
            $this->show(
                name: 'Stranger Things',
                overview: 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces, and one strange little girl.',
                episodeRunTime: 50,
                numberOfSeasons: 4,
                numberOfEpisodes: 34,
                firstAirDate: '2016-07-15',
                lastAirDate: null,
                tmdbId: 66732,
                imdbId: 'tt4574334',
            ),
            $this->show(
                name: 'Wednesday',
                overview: 'Wednesday Addams is sent to Nevermore Academy, a bizarre boarding school where she attempts to master her psychic powers, solve a supernatural mystery, and navigate new relationships.',
                episodeRunTime: 45,
                numberOfSeasons: 1,
                numberOfEpisodes: 8,
                firstAirDate: '2022-11-23',
                lastAirDate: null,
                tmdbId: 119051,
                imdbId: 'tt13443470',
            ),
            $this->show(
                name: 'The Last of Us',
                overview: 'Twenty years after modern civilization has been destroyed, Joel, a hardened survivor, is hired to smuggle Ellie, a 14-year-old girl, out of an oppressive quarantine zone.',
                episodeRunTime: 55,
                numberOfSeasons: 2,
                numberOfEpisodes: 16,
                firstAirDate: '2023-01-15',
                lastAirDate: null,
                tmdbId: 100088,
                imdbId: 'tt3581920',
            ),
            $this->show(
                name: 'House of the Dragon',
                overview: 'The Targaryen dynasty is at the absolute apex of its power, with more than 15 dragons under their yoke. Most combatants combust before fighting.',
                episodeRunTime: 60,
                numberOfSeasons: 2,
                numberOfEpisodes: 18,
                firstAirDate: '2022-08-21',
                lastAirDate: null,
                tmdbId: 94997,
                imdbId: 'tt11198330',
            ),
            $this->show(
                name: 'Breaking Bad',
                overview: 'A high school chemistry teacher diagnosed with inoperable lung cancer turns to manufacturing and selling methamphetamine in order to secure his family\'s future.',
                episodeRunTime: 45,
                numberOfSeasons: 5,
                numberOfEpisodes: 62,
                firstAirDate: '2008-01-20',
                lastAirDate: '2013-09-29',
                tmdbId: 1396,
                imdbId: 'tt0903747',
            ),
            $this->show(
                name: 'Game of Thrones',
                overview: 'Seven noble families fight for control of the mythical land of Westeros. Friction between the houses leads to full-scale war.',
                episodeRunTime: 60,
                numberOfSeasons: 8,
                numberOfEpisodes: 73,
                firstAirDate: '2011-04-17',
                lastAirDate: '2019-05-19',
                tmdbId: 1399,
                imdbId: 'tt0944947',
            ),
            $this->show(
                name: 'The Bear',
                overview: 'A young chef from the fine dining world returns to Chicago to run his family\'s sandwich shop after a heartbreaking death in his family.',
                episodeRunTime: 30,
                numberOfSeasons: 3,
                numberOfEpisodes: 28,
                firstAirDate: '2022-06-23',
                lastAirDate: null,
                tmdbId: 136315,
                imdbId: 'tt14452776',
            ),
            $this->show(
                name: 'Euphoria',
                overview: 'A look at life for a group of high school students as they grapple with issues of drugs, sex, and violence.',
                episodeRunTime: 55,
                numberOfSeasons: 2,
                numberOfEpisodes: 16,
                firstAirDate: '2019-06-16',
                lastAirDate: null,
                tmdbId: 85552,
                imdbId: 'tt8772296',
            ),
            $this->show(
                name: 'Arcane',
                overview: 'Amid the stark discord of twin cities Piltover and Zaun, two sisters fight on rival sides of a war between magic technologies and clashing convictions.',
                episodeRunTime: 40,
                numberOfSeasons: 2,
                numberOfEpisodes: 18,
                firstAirDate: '2021-11-06',
                lastAirDate: '2024-11-23',
                tmdbId: 94605,
                imdbId: 'tt11126994',
            ),
            $this->show(
                name: 'The Mandalorian',
                overview: 'After the fall of the Galactic Empire, lawlessness has spread throughout the galaxy. A lone gunfighter makes his way through the outer reaches.',
                episodeRunTime: 40,
                numberOfSeasons: 3,
                numberOfEpisodes: 24,
                firstAirDate: '2019-11-12',
                lastAirDate: null,
                tmdbId: 82856,
                imdbId: 'tt8111088',
            ),
        ];
    }

    protected function show(
        string $name,
        string $overview,
        int $episodeRunTime,
        int $numberOfSeasons,
        int $numberOfEpisodes,
        string $firstAirDate,
        ?string $lastAirDate,
        int $tmdbId,
        string $imdbId,
    ): array {
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'overview' => $overview,
            'episode_run_time' => $episodeRunTime,
            'number_of_seasons' => $numberOfSeasons,
            'number_of_episodes' => $numberOfEpisodes,
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'tmdb_id' => $tmdbId,
            'imdb_id' => $imdbId,
            'is_published' => true,
            'is_hidden' => false,
        ];
    }
}
