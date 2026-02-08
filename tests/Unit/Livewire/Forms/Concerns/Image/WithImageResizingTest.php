<?php

use App\Livewire\Forms\Concerns\Image\ImageTranscoder;
use App\Livewire\Forms\Concerns\Image\WithImageResizing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tests\TestCase;

uses(TestCase::class);

it('stores non-gif uploads with the standard picture profile', function (): void {
    Storage::fake('public');

    $file = toTemporaryUploadedFile(UploadedFile::fake()->image('portrait.jpg', 2000, 1000));
    $path = imageResizingHarness()->storeStandard($file, 'test-person');

    expect($path)->toBe('people/test-person.jpg');

    Storage::disk('public')->assertExists([
        'people/test-person.jpg',
        'people/test-person.webp',
        'people/test-person.avif',
    ]);
    Storage::disk('public')->assertMissing('people/test-person.gif');

    [$width, $height] = traitPictureDimensions('people/test-person.jpg');

    expect($width)->toBe(512)
        ->and($height)->toBe(512);
});

it('stores gif uploads as animated gif primary with flattened alternate variants', function (): void {
    Storage::fake('public');

    $file = toTemporaryUploadedFile(traitAnimatedGifUpload(320, 240));
    $path = imageResizingHarness()->storeStandard($file, 'animated-person');

    expect($path)->toBe('people/animated-person.gif');

    Storage::disk('public')->assertExists([
        'people/animated-person.gif',
        'people/animated-person.jpg',
        'people/animated-person.webp',
        'people/animated-person.avif',
    ]);

    $gifImage = traitReadStoredImage('people/animated-person.gif');
    $jpgImage = traitReadStoredImage('people/animated-person.jpg');
    $webpImage = traitReadStoredImage('people/animated-person.webp');
    $avifImage = traitReadStoredImage('people/animated-person.avif');

    expect($gifImage->isAnimated())->toBeTrue()
        ->and($gifImage->core()->count())->toBeGreaterThan(1)
        ->and($jpgImage->isAnimated())->toBeFalse()
        ->and($jpgImage->core()->count())->toBe(1)
        ->and($webpImage->isAnimated())->toBeFalse()
        ->and($webpImage->core()->count())->toBe(1)
        ->and($avifImage->isAnimated())->toBeFalse()
        ->and($avifImage->core()->count())->toBe(1);
});

it('stores images using a custom transcoding profile', function (): void {
    Storage::fake('public');

    $file = toTemporaryUploadedFile(UploadedFile::fake()->image('landscape.png', 1200, 600));
    $path = imageResizingHarness()->storeCustom(
        $file,
        'custom-person',
        'profiles',
        fn (ImageTranscoder $transcoder): ImageTranscoder => $transcoder
            ->scaleDown(width: 300)
            ->variant('webp', fn ($image) => $image->toWebp(75))
            ->primary('webp'),
    );

    expect($path)->toBe('profiles/custom-person.webp');

    Storage::disk('public')->assertExists('profiles/custom-person.webp');

    [$width, $height] = traitPictureDimensions('profiles/custom-person.webp');

    expect($width)->toBe(300)
        ->and($height)->toBe(150);
});

function imageResizingHarness(): object
{
    return new class
    {
        use WithImageResizing;

        public function storeStandard(
            TemporaryUploadedFile $file,
            string $basename,
            string $directory = 'people',
            string $disk = 'public',
        ): string {
            return $this->storeStandardPicture($file, $basename, $directory, $disk);
        }

        public function storeCustom(
            TemporaryUploadedFile $file,
            string $basename,
            string $directory,
            callable $profile,
            string $disk = 'public',
        ): string {
            return $this->storeTranscodedImage($file, $basename, $directory, $profile, $disk);
        }
    };
}

function toTemporaryUploadedFile(UploadedFile $file): TemporaryUploadedFile
{
    FileUploadConfiguration::storage();

    $storedPath = FileUploadConfiguration::storeTemporaryFile($file, FileUploadConfiguration::disk());
    $directoryPrefix = trim(FileUploadConfiguration::directory(), '/').'/';
    $livewirePath = str_starts_with($storedPath, $directoryPrefix)
        ? substr($storedPath, strlen($directoryPrefix))
        : basename($storedPath);

    return TemporaryUploadedFile::createFromLivewire($livewirePath);
}

function traitReadStoredImage(string $path): ImageInterface
{
    $manager = new ImageManager(new Driver);

    return $manager->read(Storage::disk('public')->get($path));
}

function traitPictureDimensions(string $path): array
{
    $image = traitReadStoredImage($path);

    return [$image->width(), $image->height()];
}

function traitAnimatedGifUpload(int $width, int $height): UploadedFile
{
    $manager = new ImageManager(new Driver);
    $gif = (string) $manager->animate(function ($animation) use ($manager, $width, $height): void {
        $animation->add($manager->create($width, $height)->fill('ff0000'), 0.2);
        $animation->add($manager->create($width, $height)->fill('00ff00'), 0.2);
    })->toGif();

    $tempPath = tempnam(sys_get_temp_dir(), 'trait-gif-');
    file_put_contents($tempPath, $gif);

    return new UploadedFile($tempPath, 'animated.gif', 'image/gif', test: true);
}
