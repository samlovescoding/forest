<?php

namespace App\Livewire\Forms\Concerns\Image;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait WithImageResizing
{
  /**
   * @param  callable(ImageTranscoder): ImageTranscoder  $profile
   */
  protected function storeTranscodedImage(
    TemporaryUploadedFile $file,
    string $basename,
    string $directory,
    callable $profile,
    string $disk = 'public',
  ): string {
    $transcoder = $this->transcodeUploadedImage($file)
        ->disk($disk)
        ->directory($directory)
        ->basename($basename);

    return $profile($transcoder)->store()->primaryPath();
  }

  protected function storePersonPicture(
    TemporaryUploadedFile $file,
    string $basename,
    string $directory = 'people',
    string $disk = 'public',
  ): string {
    return $this->storeTranscodedImage(
      file: $file,
      basename: $basename,
      directory: $directory,
      profile: fn (ImageTranscoder $transcoder): ImageTranscoder => $this->applyPersonPictureProfile($transcoder, $file),
      disk: $disk,
    );
  }

  protected function applyPersonPictureProfile(ImageTranscoder $transcoder, TemporaryUploadedFile $file): ImageTranscoder
  {
    $transcoder = $transcoder->coverDown(512, 512, 'center');

    if ($this->isGifUpload($file)) {
      return $transcoder
          ->variant('gif', fn ($image) => $image->toGif())
          ->variant('jpg', fn ($image) => $image->removeAnimation(0)->toJpeg(90))
          ->variant('webp', fn ($image) => $image->removeAnimation(0)->toWebp(85))
          ->variant('avif', fn ($image) => $image->removeAnimation(0)->toAvif(65))
          ->primary('gif');
    }

    return $transcoder
        ->variant('jpg', fn ($image) => $image->toJpeg(90))
        ->variant('webp', fn ($image) => $image->toWebp(85))
        ->variant('avif', fn ($image) => $image->toAvif(65))
        ->primary('jpg');
  }

  protected function isGifUpload(TemporaryUploadedFile $file): bool
  {
    $mimeType = strtolower((string) $file->getMimeType());
    $extension = strtolower((string) $file->getClientOriginalExtension());

    return $mimeType === 'image/gif' || $extension === 'gif';
  }

  protected function transcodeUploadedImage(TemporaryUploadedFile $file): ImageTranscoder
  {
    return new ImageTranscoder($file);
  }
}
