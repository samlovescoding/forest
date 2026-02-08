<?php

namespace App\Livewire\Forms\Concerns\Image;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait WithImageResizing
{
    protected function transcodeUploadedImage(TemporaryUploadedFile $file): ImageTranscoder
    {
        return new ImageTranscoder($file);
    }
}
