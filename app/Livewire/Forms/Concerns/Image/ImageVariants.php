<?php

namespace App\Livewire\Forms\Concerns\Image;

use InvalidArgumentException;

class ImageVariants
{
    /**
     * @param  array<string, string>  $paths
     */
    public function __construct(
        protected array $paths,
        protected string $primaryExtension,
    ) {
        $normalizedPaths = [];

        foreach ($paths as $extension => $path) {
            $normalizedPaths[$this->normalizeExtension($extension)] = $path;
        }

        $this->paths = $normalizedPaths;
        $this->primaryExtension = $this->normalizeExtension($primaryExtension);

        if (! array_key_exists($this->primaryExtension, $this->paths)) {
            throw new InvalidArgumentException('Primary extension must exist in stored image paths.');
        }
    }

    public function primaryPath(): string
    {
        return $this->paths[$this->primaryExtension];
    }

    public function primaryExtension(): string
    {
        return $this->primaryExtension;
    }

    public function path(string $extension): ?string
    {
        return $this->paths[$this->normalizeExtension($extension)] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function paths(): array
    {
        return $this->paths;
    }

    private function normalizeExtension(string $extension): string
    {
        $normalizedExtension = strtolower(ltrim(trim($extension), '.'));

        if ($normalizedExtension === '') {
            throw new InvalidArgumentException('Variant extension cannot be empty.');
        }

        return $normalizedExtension;
    }
}
