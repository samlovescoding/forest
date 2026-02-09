<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Show;
use App\Services\TMDbService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImportShowSeasonsAndEpisodes implements ShouldQueue
{
    use Queueable;

    public function __construct(public Show $show) {}

    public function handle(TMDbService $tmdb): void
    {
        $showData = $tmdb->getTvShow($this->show->tmdb_id);
        $numberOfSeasons = $showData['number_of_seasons'] ?? 0;

        for ($seasonNumber = 1; $seasonNumber <= $numberOfSeasons; $seasonNumber++) {
            $seasonData = $tmdb->getTvSeason($this->show->tmdb_id, $seasonNumber);

            if (Season::query()->where('tmdb_id', $seasonData['id'])->exists()) {
                continue;
            }

            $posterPath = null;
            if (! empty($seasonData['poster_path'])) {
                $posterPath = $this->storeImageFromUrl(
                    TMDbService::imageUrl($seasonData['poster_path']),
                    'seasons',
                    $this->show->slug.'-season-'.$seasonNumber,
                    500,
                    750,
                );
            }

            $season = Season::query()->create([
                'show_id' => $this->show->id,
                'name' => $seasonData['name'],
                'slug' => Season::createScopedSlug($seasonData['name'], $this->show->id),
                'overview' => $seasonData['overview'] ?? null,
                'season_number' => $seasonData['season_number'],
                'episode_count' => $seasonData['episodes'] ? count($seasonData['episodes']) : 0,
                'air_date' => $seasonData['air_date'] ?? null,
                'vote_average' => $seasonData['vote_average'] ?? null,
                'poster_path' => $posterPath,
                'tmdb_id' => $seasonData['id'],
            ]);

            foreach ($seasonData['episodes'] ?? [] as $episodeData) {
                if (Episode::query()->where('tmdb_id', $episodeData['id'])->exists()) {
                    continue;
                }

                $stillPath = null;
                if (! empty($episodeData['still_path'])) {
                    $stillPath = $this->storeImageFromUrl(
                        TMDbService::imageUrl($episodeData['still_path']),
                        'episodes',
                        $this->show->slug.'-s'.$seasonNumber.'e'.$episodeData['episode_number'],
                        1920,
                        1080,
                    );
                }

                Episode::query()->create([
                    'season_id' => $season->id,
                    'show_id' => $this->show->id,
                    'name' => $episodeData['name'],
                    'slug' => Episode::createScopedSlug($episodeData['name'], $season->id),
                    'overview' => $episodeData['overview'] ?? null,
                    'episode_number' => $episodeData['episode_number'],
                    'season_number' => $episodeData['season_number'],
                    'runtime' => $episodeData['runtime'] ?? null,
                    'air_date' => $episodeData['air_date'] ?? null,
                    'production_code' => $episodeData['production_code'] ?? null,
                    'vote_count' => $episodeData['vote_count'] ?? null,
                    'vote_average' => $episodeData['vote_average'] ?? null,
                    'still_path' => $stillPath,
                    'tmdb_id' => $episodeData['id'],
                ]);
            }
        }
    }

    private function storeImageFromUrl(string $url, string $folder, string $name, int $width, int $height): ?string
    {
        $response = Http::get($url);

        if ($response->failed()) {
            return null;
        }

        $imageContent = $response->body();
        $manager = new ImageManager(new Driver);
        $storage = Storage::disk('public');

        $fileBase = $folder.'/'.$name.'.';
        $primaryFileName = $fileBase.'jpg';

        $source = $manager->read($imageContent)->coverDown($width, $height, 'center');

        $storage->put($primaryFileName, $source->toJpeg(80));
        $storage->put($fileBase.'avif', $source->toAvif(65));
        $storage->put($fileBase.'webp', $source->toWebp(70));
        Storage::disk('local')->put($primaryFileName, $imageContent);

        return $primaryFileName;
    }
}
