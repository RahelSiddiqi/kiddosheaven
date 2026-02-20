<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /** Maximum output file size in bytes (1 MB) */
    const MAX_BYTES = 1 * 1024 * 1024;

    /** Starting encode quality (0–100) */
    const QUALITY_START = 85;

    /** Minimum quality we'll drop to */
    const QUALITY_MIN = 30;

    /** Quality step per iteration */
    const QUALITY_STEP = 10;

    /**
     * Compress and store an uploaded image to the public disk.
     *
     * The image is re-encoded as WebP at decreasing quality levels until the
     * file size is ≤ MAX_BYTES (1 MB), while keeping the original resolution.
     *
     * @param  UploadedFile  $file
     * @param  string        $directory  Sub-directory inside storage/app/public/
     * @return string        Public-disk path (e.g. "products/abc123.webp")
     */
    public function store(UploadedFile $file, string $directory = 'products'): string
    {
        $filename = $directory . '/' . Str::random(20) . '.webp';

        $image   = Image::read($file);
        $quality = self::QUALITY_START;

        do {
            $encoded = $image->toWebp(quality: $quality);
            $bytes   = strlen((string) $encoded);

            if ($bytes <= self::MAX_BYTES || $quality <= self::QUALITY_MIN) {
                break;
            }

            $quality -= self::QUALITY_STEP;
        } while ($quality >= self::QUALITY_MIN);

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}
