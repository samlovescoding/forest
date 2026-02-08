<?php

use App\Livewire\Forms\Concerns\Image\ImageTranscoder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Tests\TestCase;

uses(TestCase::class);

it('stores declared variants and returns primary and variant paths', function (): void {
    Storage::fake('public');

    $upload = UploadedFile::fake()->image('portrait.jpg', 600, 400);

    $result = (new ImageTranscoder($upload))
        ->disk('public')
        ->directory('people')
        ->basename('test-person')
        ->coverDown(512, 512, 'center')
        ->variant('jpg', fn ($image) => $image->toJpeg(92))
        ->variant('webp', fn ($image) => $image->toWebp(80))
        ->variant('avif', fn ($image) => $image->toAvif(70))
        ->primary('jpg')
        ->store();

    expect($result->primaryExtension())->toBe('jpg')
        ->and($result->primaryPath())->toBe('people/test-person.jpg')
        ->and($result->path('webp'))->toBe('people/test-person.webp')
        ->and($result->path('avif'))->toBe('people/test-person.avif');

    Storage::disk('public')->assertExists([
        'people/test-person.jpg',
        'people/test-person.webp',
        'people/test-person.avif',
    ]);
});

it('throws when storing without variants', function (): void {
    Storage::fake('public');

    expect(fn () => (new ImageTranscoder(UploadedFile::fake()->image('portrait.jpg')))
        ->disk('public')
        ->directory('people')
        ->basename('test-person')
        ->primary('jpg')
        ->store())
        ->toThrow(\InvalidArgumentException::class, 'At least one output variant must be defined.');
});

it('throws when storing without basename', function (): void {
    Storage::fake('public');

    expect(fn () => (new ImageTranscoder(UploadedFile::fake()->image('portrait.jpg')))
        ->disk('public')
        ->directory('people')
        ->variant('jpg', fn ($image) => $image->toJpeg(92))
        ->primary('jpg')
        ->store())
        ->toThrow(\InvalidArgumentException::class, 'Basename is required before storing transcoded images.');
});

it('throws when primary extension is not declared as variant', function (): void {
    Storage::fake('public');

    expect(fn () => (new ImageTranscoder(UploadedFile::fake()->image('portrait.jpg')))
        ->disk('public')
        ->directory('people')
        ->basename('test-person')
        ->variant('jpg', fn ($image) => $image->toJpeg(92))
        ->primary('webp')
        ->store())
        ->toThrow(\InvalidArgumentException::class, 'Primary extension must match a declared output variant.');
});

it('isolates each variant callback so frame flattening does not leak to other variants', function (): void {
    Storage::fake('public');

    $upload = transcoderAnimatedGifUpload(220, 320);

    (new ImageTranscoder($upload))
        ->disk('public')
        ->directory('people')
        ->basename('animated-test')
        ->coverDown(512, 512, 'center')
        ->variant('gif', fn ($image) => $image->toGif())
        ->variant('jpg', fn ($image) => $image->removeAnimation(0)->toJpeg(92))
        ->primary('gif')
        ->store();

    $gifImage = transcoderReadStoredImage('people/animated-test.gif');
    $jpgImage = transcoderReadStoredImage('people/animated-test.jpg');

    expect($gifImage->isAnimated())->toBeTrue()
        ->and($gifImage->core()->count())->toBeGreaterThan(1)
        ->and($jpgImage->isAnimated())->toBeFalse()
        ->and($jpgImage->core()->count())->toBe(1);
});

function transcoderReadStoredImage(string $path): ImageInterface
{
    $manager = new ImageManager(new Driver);

    return $manager->read(Storage::disk('public')->get($path));
}

function transcoderAnimatedGifUpload(int $width, int $height): UploadedFile
{
    $manager = new ImageManager(new Driver);
    $gif = (string) $manager->animate(function ($animation) use ($manager, $width, $height): void {
        $animation->add($manager->create($width, $height)->fill('ff0000'), 0.2);
        $animation->add($manager->create($width, $height)->fill('00ff00'), 0.2);
    })->toGif();

    $tempPath = tempnam(sys_get_temp_dir(), 'transcoder-gif-');
    file_put_contents($tempPath, $gif);

    return new UploadedFile($tempPath, 'animated.gif', 'image/gif', test: true);
}
