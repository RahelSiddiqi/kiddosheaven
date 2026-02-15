<?php

namespace App\Support\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Trait for models that have media files (images, videos, documents).
 */
trait HasMediaFiles
{
    public function getImageUrl(?string $path, string $default = '/images/placeholder.jpg'): string
    {
        if (!$path) {
            return $default;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::url($path);
    }

    public function getFullImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::path($path);
    }

    public function deleteImage(?string $path): bool
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        return Storage::delete($path);
    }
}
