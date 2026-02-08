<?php

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('builds picture urls from the model with canonical fallback behavior', function (): void {
    $jpgPerson = createPerson(['picture' => 'people/jane-doe.jpg']);
    $gifPerson = createPerson([
        'name' => 'Animated',
        'slug' => 'animated',
        'picture' => 'people/animated.gif',
    ]);

    expect($jpgPerson->pictureUrl())
        ->toBe(Storage::url('people/jane-doe.jpg'))
        ->and($jpgPerson->pictureUrl('webp'))
        ->toBe(Storage::url('people/jane-doe.webp'))
        ->and($jpgPerson->pictureUrl('avif'))
        ->toBe(Storage::url('people/jane-doe.avif'))
        ->and($jpgPerson->pictureUrl('unsupported'))
        ->toBe(Storage::url('people/jane-doe.jpg'))
        ->and($gifPerson->pictureUrl())
        ->toBe(Storage::url('people/animated.gif'))
        ->and($gifPerson->pictureUrl('jpg'))
        ->toBe(Storage::url('people/animated.jpg'))
        ->and($gifPerson->pictureUrl('webp'))
        ->toBe(Storage::url('people/animated.webp'));

    expect(createPerson([
        'name' => 'No Picture',
        'slug' => 'no-picture',
        'picture' => null,
    ])->pictureUrl())->toBeNull();
});

it('stores non-gif uploads as jpg primary with webp and avif variants', function (): void {
    Storage::fake('public');

    $component = Livewire::test('pages::people.create')
        ->set('form.name', 'Ariana Grande')
        ->set('form.full_name', 'Ariana Grande-Butera')
        ->set('form.birth_date', '1993-06-26')
        ->set('form.death_date', '')
        ->set('form.gender', 'female')
        ->set('form.sexuality', 'straight')
        ->set('form.birth_country', 'United States of America')
        ->set('form.birth_city', 'Boca Raton')
        ->set('form.picture', UploadedFile::fake()->image('portrait.jpg', 200, 300))
        ->call('submit');

    $person = Person::query()->firstOrFail();
    $basePath = "people/{$person->slug}";

    $component->assertRedirect(route('people.view', $person));

    expect($person->picture)->toBe("{$basePath}.jpg");
    Storage::disk('public')->assertExists([
        "{$basePath}.jpg",
        "{$basePath}.webp",
        "{$basePath}.avif",
    ]);
    Storage::disk('public')->assertMissing("{$basePath}.gif");

    [$width, $height] = pictureDimensions("{$basePath}.jpg");

    expect($width)->toBe(200)
        ->and($height)->toBe(200);
});

it('stores gif uploads as gif primary and flattens first frame for jpg webp and avif', function (): void {
    Storage::fake('public');

    $component = Livewire::test('pages::people.create')
        ->set('form.name', 'Dancing Gif')
        ->set('form.full_name', 'Dancing Gif Person')
        ->set('form.birth_date', '1991-04-01')
        ->set('form.death_date', '')
        ->set('form.gender', 'female')
        ->set('form.sexuality', 'straight')
        ->set('form.birth_country', 'United States of America')
        ->set('form.birth_city', 'Austin')
        ->set('form.picture', animatedGifUpload(200, 300))
        ->call('submit');

    $person = Person::query()->firstOrFail();
    $basePath = "people/{$person->slug}";

    $component->assertRedirect(route('people.view', $person));

    expect($person->picture)->toBe("{$basePath}.gif");
    Storage::disk('public')->assertExists([
        "{$basePath}.gif",
        "{$basePath}.jpg",
        "{$basePath}.webp",
        "{$basePath}.avif",
    ]);

    $gifImage = readImage("{$basePath}.gif");
    $jpgImage = readImage("{$basePath}.jpg");
    $webpImage = readImage("{$basePath}.webp");
    $avifImage = readImage("{$basePath}.avif");

    expect($gifImage->isAnimated())->toBeTrue()
        ->and($gifImage->core()->count())->toBeGreaterThan(1)
        ->and($gifImage->width())->toBe(200)
        ->and($gifImage->height())->toBe(200)
        ->and($jpgImage->isAnimated())->toBeFalse()
        ->and($jpgImage->core()->count())->toBe(1)
        ->and($webpImage->isAnimated())->toBeFalse()
        ->and($webpImage->core()->count())->toBe(1)
        ->and($avifImage->isAnimated())->toBeFalse()
        ->and($avifImage->core()->count())->toBe(1);
});

it('stores square 512 images for large uploads when editing', function (): void {
    Storage::fake('public');

    $person = createPerson([
        'name' => 'Zendaya',
        'slug' => 'zendaya',
        'picture' => null,
    ]);

    $component = Livewire::test('pages::people.edit', ['person' => $person])
        ->set('form.picture', UploadedFile::fake()->image('landscape.jpg', 2000, 1000))
        ->call('submit');

    $person->refresh();
    $basePath = "people/{$person->slug}";

    $component->assertRedirect(route('people.view', $person));

    expect($person->picture)->toBe("{$basePath}.jpg");
    Storage::disk('public')->assertExists([
        "{$basePath}.jpg",
        "{$basePath}.webp",
        "{$basePath}.avif",
    ]);

    [$width, $height] = pictureDimensions("{$basePath}.jpg");

    expect($width)->toBe(512)
        ->and($height)->toBe(512);
});

function createPerson(array $overrides = []): Person
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

function pictureDimensions(string $path): array
{
    $image = readImage($path);

    return [$image->width(), $image->height()];
}

function readImage(string $path): ImageInterface
{
    $manager = new ImageManager(new Driver);

    return $manager->read(Storage::disk('public')->get($path));
}

function animatedGifUpload(int $width, int $height): UploadedFile
{
    $manager = new ImageManager(new Driver);
    $gif = (string) $manager->animate(function ($animation) use ($manager, $width, $height): void {
        $animation->add($manager->create($width, $height)->fill('ff0000'), 0.2);
        $animation->add($manager->create($width, $height)->fill('00ff00'), 0.2);
    })->toGif();

    return UploadedFile::fake()->createWithContent('animated.gif', $gif);
}
