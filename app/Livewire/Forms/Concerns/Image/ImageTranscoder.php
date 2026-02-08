<?php

namespace App\Livewire\Forms\Concerns\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use InvalidArgumentException;
use Stringable;

class ImageTranscoder
{
    protected string $disk = 'public';

    protected string $directory = '';

    protected string $basename = '';

    /** @var array<int, callable(ImageInterface): ImageInterface> */
    protected array $transforms = [];

    /** @var array<string, callable(ImageInterface): mixed> */
    protected array $variants = [];

    protected ?string $primaryExtension = null;

    public function __construct(protected UploadedFile $file) {}

    public function disk(string $disk): self
    {
        $this->disk = trim($disk);

        return $this;
    }

    public function directory(string $directory): self
    {
        $this->directory = trim($directory, '/');

        return $this;
    }

    public function basename(string $basename): self
    {
        $normalizedBasename = trim($basename);

        if (str_contains($normalizedBasename, '/') || str_contains($normalizedBasename, '\\')) {
            throw new InvalidArgumentException('Basename cannot contain directory separators.');
        }

        $this->basename = $normalizedBasename;

        return $this;
    }

    public function coverDown(int $width, int $height, string $position = 'center'): self
    {
        $this->transforms[] = static fn (ImageInterface $image): ImageInterface => $image->coverDown($width, $height, $position);

        return $this;
    }

    public function scaleDown(?int $width = null, ?int $height = null): self
    {
        $this->transforms[] = static fn (ImageInterface $image): ImageInterface => $image->scaleDown($width, $height);

        return $this;
    }

    public function resizeCanvas(?int $width = null, ?int $height = null, mixed $background = 'ffffff', string $position = 'center'): self
    {
        $this->transforms[] = static fn (ImageInterface $image): ImageInterface => $image->resizeCanvas($width, $height, $background, $position);

        return $this;
    }

    /**
     * @param  callable(ImageInterface): mixed  $encoder
     */
    public function variant(string $extension, callable $encoder): self
    {
        $this->variants[$this->normalizeExtension($extension)] = $encoder;

        return $this;
    }

    public function primary(string $extension): self
    {
        $this->primaryExtension = $this->normalizeExtension($extension);

        return $this;
    }

    public function store(): ImageVariants
    {
        $this->ensureStoreIsValid();

        $image = $this->buildImage();
        $paths = [];

        foreach ($this->variants as $extension => $encoder) {
            $path = $this->buildPath($extension);
            $encoded = $encoder(clone $image);

            Storage::disk($this->disk)->put($path, $this->normalizeEncodedOutput($encoded, $extension));

            $paths[$extension] = $path;
        }

        return new ImageVariants($paths, $this->primaryExtension);
    }

    protected function buildImage(): ImageInterface
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($this->file);

        foreach ($this->transforms as $transform) {
            $image = $transform($image);
        }

        return $image;
    }

    protected function ensureStoreIsValid(): void
    {
        if ($this->basename === '') {
            throw new InvalidArgumentException('Basename is required before storing transcoded images.');
        }

        if ($this->variants === []) {
            throw new InvalidArgumentException('At least one output variant must be defined.');
        }

        if ($this->primaryExtension === null) {
            throw new InvalidArgumentException('Primary extension must be defined before storing transcoded images.');
        }

        if (! array_key_exists($this->primaryExtension, $this->variants)) {
            throw new InvalidArgumentException('Primary extension must match a declared output variant.');
        }
    }

    protected function buildPath(string $extension): string
    {
        $basePath = "{$this->basename}.{$extension}";

        if ($this->directory === '') {
            return $basePath;
        }

        return "{$this->directory}/{$basePath}";
    }

    protected function normalizeExtension(string $extension): string
    {
        $normalizedExtension = strtolower(ltrim(trim($extension), '.'));

        if ($normalizedExtension === '') {
            throw new InvalidArgumentException('Variant extension cannot be empty.');
        }

        if (! preg_match('/^[a-z0-9]+$/', $normalizedExtension)) {
            throw new InvalidArgumentException('Variant extension must be alphanumeric.');
        }

        return $normalizedExtension;
    }

    protected function normalizeEncodedOutput(mixed $encoded, string $extension): string
    {
        if (is_string($encoded)) {
            return $encoded;
        }

        if ($encoded instanceof EncodedImageInterface) {
            return (string) $encoded;
        }

        if ($encoded instanceof Stringable) {
            return (string) $encoded;
        }

        throw new InvalidArgumentException("Encoder callback for '{$extension}' must return a string or encoded image.");
    }
}
