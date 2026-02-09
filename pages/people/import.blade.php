<?php

use App\Models\Person;
use App\Services\TMDbService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
  #[Url(except: '')]
  public string $query = '';

  public $response = null;

  public function search(): void
  {
    $tmdb = new TMDbService;
    $this->response = $tmdb->searchPeople($this->query);
  }

  public function import(int $id)
  {
    $tmdb = new TMDbService;
    $personData = $tmdb->getPerson($id);

    $slug = Person::createSlug($personData['name']);

    $picture = $this->storeImageFromUrl(
      TMDbService::imageUrl($personData['profile_path']),
      $slug,
      768,
      768,
    );

    $gender = match ($personData['gender'] ?? 0) {
      1 => 'female',
      2 => 'male',
      default => 'unknown',
    };

    $placeOfBirth = $personData['place_of_birth'] ?? '';
    $birthParts = array_map('trim', explode(',', $placeOfBirth));
    $birthCountry = count($birthParts) > 1 ? end($birthParts) : ($birthParts[0] ?? '');
    $birthCity = count($birthParts) > 1 ? $birthParts[0] : '';

    $person = Person::query()->create([
        'name' => $personData['name'],
        'slug' => $slug,
        'full_name' => $personData['name'],
        'birth_date' => $personData['birthday'],
        'death_date' => $personData['deathday'],
        'gender' => $gender,
        'sexuality' => 'straight',
        'birth_country' => $birthCountry,
        'birth_city' => $birthCity,
        'picture' => $picture,
    ]);

    $this->redirectRoute('people.view', $person);
  }

  private function storeImageFromUrl(string $url, string $name, int $width, int $height): ?string
  {
    $response = Http::get($url);

    if ($response->failed()) {
      return null;
    }

    $imageContent = $response->body();
    $manager = new ImageManager(new Driver);
    $storage = Storage::disk('public');

    $fileBase = 'people/'.$name.'.';
    $primaryFileName = $fileBase.'jpg';

    $source = $manager->read($imageContent)->coverDown($width, $height, 'center');

    $storage->put($primaryFileName, $source->toJpeg(80));
    $storage->put($fileBase.'avif', $source->toWebp(70));
    $storage->put($fileBase.'webp', $source->toAvif(65));
    Storage::disk('local')->put($primaryFileName, $imageContent);

    return $primaryFileName;
  }
};
?>

<main>
  <x-form class="flex gap-2" wire:submit="search">
    <div>
      <flux:input wire:model="query" placeholder="Search for people..."/>
    </div>
    <flux:button type="submit">Search</flux:button>
  </x-form>

  @isset($this->response)
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 mt-8">
      @foreach ($this->response['results'] as $person)
        <flux:card size="sm" class="flex h-full flex-col gap-4 overflow-hidden p-0 relative">
          <div class="aspect-square w-full overflow-hidden bg-zinc-100 dark:bg-white/10">
            <img
              src="{{ TMDbService::imageUrl($person['profile_path'])  }}"
              alt="Photo of {{ $person['name'] }}"
            />
          </div>
          <div class="min-w-0 p-4 pt-0 flex flex-col gap-4">
            <flux:heading class="truncate">
              {{ $person['name'] }}
            </flux:heading>
            <div>
              <flux:button size="xs"
                           wire:confirm="Are you sure, you want to import this person?"
                           wire:click="import({{ $person['id'] }})">
                Import
              </flux:button>
            </div>
          </div>
        </flux:card>
      @endforeach
    </div>
  @endisset
</main>
