<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TMDbService
{
  private string $apiKey;

  private string $baseUrl = 'https://api.themoviedb.org/3';

  public function __construct()
  {
    $this->initializeClient();
  }

  private function initializeClient(): void
  {
    $apiKey = config('services.tmdb.api_key');

    if (empty($apiKey)) {
      throw new Exception('TMDb API key is not configured. Please set TMDB_API_KEY in your environment variables.');
    }

    $this->apiKey = $apiKey;
  }

  private function makeRequest(string $endpoint, array $params = []): array
  {
    $params['api_key'] = $this->apiKey;

    $response = Http::get($this->baseUrl.$endpoint, $params);

    if ($response->failed()) {
      Log::error('TMDb API Error', [
          'endpoint' => $endpoint,
          'params' => $params,
          'status' => $response->status(),
          'body' => $response->body(),
      ]);

      throw new Exception('TMDb API request failed: '.$response->body());
    }

    return $response->json();
  }

  /**
   * Get TMDb configuration (image base URLs, available sizes, etc.)
   */
  public function getConfiguration(): array
  {
    return Cache::remember('tmdb_configuration', 86400, function () {
      return $this->makeRequest('/configuration');
    });
  }

  /**
   * Search for movies by query
   */
  public function searchMovies(string $query, int $page = 1): array
  {
    return $this->makeRequest('/search/movie', [
        'query' => $query,
        'page' => $page,
        'include_adult' => config('services.tmdb.include_adult', false),
        'language' => config('services.tmdb.language', 'en-US'),
    ]);
  }

  /**
   * Search for TV shows by query
   */
  public function searchTvShows(string $query, int $page = 1): array
  {
    return $this->makeRequest('/search/tv', [
        'query' => $query,
        'page' => $page,
        'language' => config('services.tmdb.language', 'en-US'),
    ]);
  }

  /**
   * Search for people by query
   */
  public function searchPeople(string $query, int $page = 1): array
  {
    return $this->makeRequest('/search/person', [
        'query' => $query,
        'page' => $page,
        'sort_by' => 'popularity.desc',
        'include_adult' => config('services.tmdb.include_adult', false),
    ]);
  }

  /**
   * Get movie details by ID
   */
  public function getMovie(int $movieId, array $appendToResponse = []): array
  {
    $cacheKey = "tmdb_movie_{$movieId}_".md5(serialize($appendToResponse));

    return Cache::remember($cacheKey, 3600, function () use ($movieId, $appendToResponse) {
      $params = [
          'language' => config('services.tmdb.language', 'en-US'),
      ];

      if (! empty($appendToResponse)) {
        $params['append_to_response'] = implode(',', $appendToResponse);
      }

      return $this->makeRequest("/movie/{$movieId}", $params);
    });
  }

  /**
   * Get movie credits (cast and crew)
   */
  public function getMovieCredits(int $movieId): array
  {
    $cacheKey = "tmdb_movie_credits_{$movieId}";

    return Cache::remember($cacheKey, 3600, function () use ($movieId) {
      return $this->makeRequest("/movie/{$movieId}/credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV show details by ID
   */
  public function getTvShow(int $tvId, array $appendToResponse = []): array
  {
    $cacheKey = "tmdb_tv_{$tvId}_".md5(serialize($appendToResponse));

    return Cache::remember($cacheKey, 3600, function () use ($tvId, $appendToResponse) {
      $params = [
          'language' => config('services.tmdb.language', 'en-US'),
      ];

      if (! empty($appendToResponse)) {
        $params['append_to_response'] = implode(',', $appendToResponse);
      }

      return $this->makeRequest("/tv/{$tvId}", $params);
    });
  }

  /**
   * Get person details by ID
   */
  public function getPerson(int $personId, array $appendToResponse = []): array
  {
    $cacheKey = "tmdb_person_{$personId}_".md5(serialize($appendToResponse));

    return Cache::remember($cacheKey, 3600, function () use ($personId, $appendToResponse) {
      $params = [
          'language' => config('services.tmdb.language', 'en-US'),
      ];

      if (! empty($appendToResponse)) {
        $params['append_to_response'] = implode(',', $appendToResponse);
      }

      return $this->makeRequest("/person/{$personId}", $params);
    });
  }

  /**
   * Get popular movies
   */
  public function getPopularMovies(int $page = 1): array
  {
    $cacheKey = "tmdb_popular_movies_page_{$page}";

    return Cache::remember($cacheKey, 1800, function () use ($page) {
      return $this->makeRequest('/movie/popular', [
          'page' => $page,
          'language' => config('services.tmdb.language', 'en-US'),
          'region' => config('services.tmdb.region', 'US'),
      ]);
    });
  }

  /**
   * Get trending movies
   */
  public function getTrendingMovies(string $timeWindow = 'day', int $page = 1): array
  {
    $cacheKey = "tmdb_trending_movies_{$timeWindow}_page_{$page}";

    return Cache::remember($cacheKey, 1800, function () use ($timeWindow, $page) {
      return $this->makeRequest("/trending/movie/{$timeWindow}", [
          'page' => $page,
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get movie images
   */
  public function getMovieImages(int $movieId): array
  {
    $cacheKey = "tmdb_movie_images_{$movieId}";

    return Cache::remember($cacheKey, 7200, function () use ($movieId) {
      return $this->makeRequest("/movie/{$movieId}/images", [
          'include_image_language' => config('services.tmdb.language', 'en-US').',null',
      ]);
    });
  }

  /**
   * Alias for buildImageUrl for convenience
   */
  public static function imageUrl($imagePath, $size = 'original')
  {
    if (! isset($imagePath)) {
      return '/';
    }
    $baseUrl = config('services.tmdb.secure_base_url', 'https://image.tmdb.org/t/p/');

    return $baseUrl.$size.$imagePath;
  }

  /**
   * Get popular poster size
   */
  public function getPopularPosterSize(): string
  {
    return 'w500';
  }

  /**
   * Get popular backdrop size
   */
  public function getPopularBackdropSize(): string
  {
    return 'w1280';
  }

  /**
   * Get popular profile size
   */
  public function getPopularProfileSize(): string
  {
    return 'w185';
  }

  /**
   * Get TV season details by ID
   */
  public function getTvSeason(int $tvId, int $seasonNumber, array $appendToResponse = []): array
  {
    $cacheKey = "tmdb_tv_{$tvId}_season_{$seasonNumber}_".md5(serialize($appendToResponse));

    return Cache::remember($cacheKey, 3600, function () use ($tvId, $seasonNumber, $appendToResponse) {
      $params = [
          'language' => config('services.tmdb.language', 'en-US'),
      ];

      if (! empty($appendToResponse)) {
        $params['append_to_response'] = implode(',', $appendToResponse);
      }

      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}", $params);
    });
  }

  /**
   * Get TV season credits (cast and crew)
   */
  public function getTvSeasonCredits(int $tvId, int $seasonNumber): array
  {
    $cacheKey = "tmdb_tv_season_credits_{$tvId}_{$seasonNumber}";

    return Cache::remember($cacheKey, 3600, function () use ($tvId, $seasonNumber) {
      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV episode details by ID
   */
  public function getTvEpisode(int $tvId, int $seasonNumber, int $episodeNumber, array $appendToResponse = []): array
  {
    $cacheKey = "tmdb_tv_{$tvId}_season_{$seasonNumber}_episode_{$episodeNumber}_".md5(serialize($appendToResponse));

    return Cache::remember($cacheKey, 3600, function () use ($tvId, $seasonNumber, $episodeNumber, $appendToResponse) {
      $params = [
          'language' => config('services.tmdb.language', 'en-US'),
      ];

      if (! empty($appendToResponse)) {
        $params['append_to_response'] = implode(',', $appendToResponse);
      }

      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/episode/{$episodeNumber}", $params);
    });
  }

  /**
   * Get TV episode credits (cast and crew)
   */
  public function getTvEpisodeCredits(int $tvId, int $seasonNumber, int $episodeNumber): array
  {
    $cacheKey = "tmdb_tv_episode_credits_{$tvId}_{$seasonNumber}_{$episodeNumber}";

    return Cache::remember($cacheKey, 3600, function () use ($tvId, $seasonNumber, $episodeNumber) {
      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/episode/{$episodeNumber}/credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV show credits (cast and crew)
   */
  public function getTvCredits(int $tvId): array
  {
    $cacheKey = "tmdb_tv_credits_{$tvId}";

    return Cache::remember($cacheKey, 3600, function () use ($tvId) {
      return $this->makeRequest("/tv/{$tvId}/credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get popular TV shows
   */
  public function getPopularTvShows(int $page = 1): array
  {
    $cacheKey = "tmdb_popular_tv_page_{$page}";

    return Cache::remember($cacheKey, 1800, function () use ($page) {
      return $this->makeRequest('/tv/popular', [
          'page' => $page,
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get trending TV shows
   */
  public function getTrendingTvShows(string $timeWindow = 'day', int $page = 1): array
  {
    $cacheKey = "tmdb_trending_tv_{$timeWindow}_page_{$page}";

    return Cache::remember($cacheKey, 1800, function () use ($timeWindow, $page) {
      return $this->makeRequest("/trending/tv/{$timeWindow}", [
          'page' => $page,
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV show images
   */
  public function getTvImages(int $tvId): array
  {
    $cacheKey = "tmdb_tv_images_{$tvId}";

    return Cache::remember($cacheKey, 7200, function () use ($tvId) {
      return $this->makeRequest("/tv/{$tvId}/images", [
          'include_image_language' => config('services.tmdb.language', 'en-US').',null',
      ]);
    });
  }

  /**
   * Get person images
   */
  public function getPersonImages(int $personId): array
  {
    $cacheKey = "tmdb_person_images_{$personId}";

    return Cache::remember($cacheKey, 7200, function () use ($personId) {
      return $this->makeRequest("/person/{$personId}/images");
    });
  }

  /**
   * Get person movie credits
   */
  public function getPersonMovieCredits(int $personId): array
  {
    $cacheKey = "tmdb_person_movie_credits_{$personId}";

    return Cache::remember($cacheKey, 3600, function () use ($personId) {
      return $this->makeRequest("/person/{$personId}/movie_credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get person TV credits
   */
  public function getPersonTvCredits(int $personId): array
  {
    $cacheKey = "tmdb_person_tv_credits_{$personId}";

    return Cache::remember($cacheKey, 3600, function () use ($personId) {
      return $this->makeRequest("/person/{$personId}/tv_credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get person combined credits (movies and TV shows together)
   */
  public function getPersonCombinedCredits(int $personId): array
  {
    $cacheKey = "tmdb_person_combined_credits_{$personId}";

    return Cache::remember($cacheKey, 3600, function () use ($personId) {
      return $this->makeRequest("/person/{$personId}/combined_credits", [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV season images
   */
  public function getTvSeasonImages(int $tvId, int $seasonNumber): array
  {
    $cacheKey = "tmdb_tv_season_images_{$tvId}_{$seasonNumber}";

    return Cache::remember($cacheKey, 7200, function () use ($tvId, $seasonNumber) {
      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/images", [
          'include_image_language' => config('services.tmdb.language', 'en-US').',null',
      ]);
    });
  }

  /**
   * Get TV episode images
   */
  public function getTvEpisodeImages(int $tvId, int $seasonNumber, int $episodeNumber): array
  {
    $cacheKey = "tmdb_tv_episode_images_{$tvId}_{$seasonNumber}_{$episodeNumber}";

    return Cache::remember($cacheKey, 7200, function () use ($tvId, $seasonNumber, $episodeNumber) {
      return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/episode/{$episodeNumber}/images", [
          'include_image_language' => config('services.tmdb.language', 'en-US').',null',
      ]);
    });
  }

  /**
   * Get movie genres list
   */
  public function getMovieGenres(): array
  {
    return Cache::remember('tmdb_movie_genres', 86400, function () {
      return $this->makeRequest('/genre/movie/list', [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get TV genres list
   */
  public function getTvGenres(): array
  {
    return Cache::remember('tmdb_tv_genres', 86400, function () {
      return $this->makeRequest('/genre/tv/list', [
          'language' => config('services.tmdb.language', 'en-US'),
      ]);
    });
  }

  /**
   * Get languages list from configuration
   */
  public function getLanguages(): array
  {
    return Cache::remember('tmdb_languages', 86400, function () {
      return $this->makeRequest('/configuration/languages');
    });
  }

  /**
   * Get countries list from configuration
   */
  public function getCountries(): array
  {
    return Cache::remember('tmdb_countries', 86400, function () {
      return $this->makeRequest('/configuration/countries');
    });
  }

  /**
   * Get production companies from popular movies (since TMDb doesn't have a companies list endpoint)
   */
  public function getProductionCompaniesFromPopularContent(): array
  {
    return Cache::remember('tmdb_production_companies_from_popular', 3600, function () {
      $companies = [];

      try {
        // Get companies from popular movies
        $popularMovies = $this->getPopularMovies(1);
        foreach ($popularMovies['results'] ?? [] as $movie) {
          $movieDetails = $this->getMovie($movie['id']);
          if (isset($movieDetails['production_companies'])) {
            foreach ($movieDetails['production_companies'] as $company) {
              $companies[$company['id']] = $company;
            }
          }
        }

        // Get companies from popular TV shows
        $popularShows = $this->getPopularTvShows(1);
        foreach ($popularShows['results'] ?? [] as $show) {
          $showDetails = $this->getTvShow($show['id']);
          if (isset($showDetails['production_companies'])) {
            foreach ($showDetails['production_companies'] as $company) {
              $companies[$company['id']] = $company;
            }
          }
        }

        return array_values($companies);
      } catch (Exception $e) {
        Log::warning('Failed to fetch production companies from popular content', [
            'error' => $e->getMessage(),
        ]);

        return [];
      }
    });
  }

  /**
   * Clear TMDb cache
   */
  public function clearCache(): void
  {
    $patterns = [
        'tmdb_configuration',
        'tmdb_movie_*',
        'tmdb_tv_*',
        'tmdb_person_*',
        'tmdb_popular_*',
        'tmdb_trending_*',
    ];

    foreach ($patterns as $pattern) {
      if (str_contains($pattern, '*')) {
        // For patterns with wildcards, we'd need to implement a cache tag system
        // For now, we'll just clear the specific known keys
        continue;
      }
      Cache::forget($pattern);
    }
  }

  /**
   * Get the underlying HTTP client for advanced usage
   */
  public function getHttpClient(): PendingRequest
  {
    return Http::baseUrl($this->baseUrl)
        ->withQueryParameters(['api_key' => $this->apiKey]);
  }
}
