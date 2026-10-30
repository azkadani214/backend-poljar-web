<?php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;

class ImageUploadService
{
    /**
     * Upload image with thumbnails (converted to WebP)
     */
    public function upload(
        UploadedFile $file,
        string $directory = 'images',
        array $sizes = []
    ): array {
        $filename = $this->generateFilename($file); // Always returns .webp now
        $disk = $this->disk();
        $relativeStoragePath = "{$directory}/{$filename}";

        // Process original image: Convert to WebP and resize if too large
        $image = Image::read($file->getRealPath());

        // Max width for "original" to prevent massive files
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        $encoded = (string) $image->toWebp(80);
        $this->put($disk, $relativeStoragePath, $encoded);

        $result = [
            'path' => $relativeStoragePath,
            'url' => Storage::disk($disk)->url($relativeStoragePath),
            'filename' => $filename,
            'size' => strlen($encoded),
            'mime_type' => 'image/webp',
            'original_name' => $file->getClientOriginalName(),
        ];

        // Generate thumbnails if sizes provided
        if (! empty($sizes)) {
            $result['thumbnails'] = $this->generateThumbnails($file, $directory, $filename, $sizes);
        }

        return $result;
    }

    /**
     * Upload avatar with circular crop
     */
    public function uploadAvatar(UploadedFile $file, string $directory = 'avatars'): array
    {
        $sizes = [
            'thumbnail' => ['width' => 50, 'height' => 50],
            'small' => ['width' => 100, 'height' => 100],
            'medium' => ['width' => 200, 'height' => 200],
            'large' => ['width' => 400, 'height' => 400],
        ];

        return $this->upload($file, $directory, $sizes);
    }

    /**
     * Upload news/blog cover image
     */
    public function uploadCoverImage(UploadedFile $file, string $directory = 'covers'): array
    {
        $sizes = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'small' => ['width' => 400, 'height' => 300],
            'medium' => ['width' => 800, 'height' => 600],
            'large' => ['width' => 1200, 'height' => 900],
        ];

        return $this->upload($file, $directory, $sizes);
    }

    /**
     * Delete image and its thumbnails
     */
    public function delete(string $path): bool
    {
        $disk = $this->disk();

        if (! Storage::disk($disk)->exists($path)) {
            return false;
        }

        // Delete original
        Storage::disk($disk)->delete($path);

        // Delete thumbnails
        $directory = dirname($path);
        $filename = basename($path);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        $suffixes = ['thumbnail', 'small', 'medium', 'large'];

        foreach ($suffixes as $suffix) {
            $thumbnailPath = "{$directory}/{$name}_{$suffix}.webp";
            if (Storage::disk($disk)->exists($thumbnailPath)) {
                Storage::disk($disk)->delete($thumbnailPath);
            }
        }

        return true;
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $filename = Str::uuid().'_'.time();

        return "{$filename}.webp";
    }

    /**
     * Generate thumbnails
     */
    private function generateThumbnails(
        UploadedFile $file,
        string $directory,
        string $filename,
        array $sizes
    ): array {
        $thumbnails = [];
        $disk = $this->disk();

        foreach ($sizes as $sizeName => $dimensions) {
            $thumbnailFilename = $this->getThumbnailFilename($filename, $sizeName);
            $thumbnailPath = "{$directory}/{$thumbnailFilename}";

            // Create thumbnail using Intervention Image
            $image = Image::read($file->getRealPath());

            if (isset($dimensions['width']) && isset($dimensions['height'])) {
                $image->cover($dimensions['width'], $dimensions['height']);
            } elseif (isset($dimensions['width'])) {
                $image->resize(width: $dimensions['width']);
            }

            $this->put($disk, $thumbnailPath, (string) $image->toWebp(80));

            $thumbnails[$sizeName] = [
                'path' => $thumbnailPath,
                'url' => Storage::disk($disk)->url($thumbnailPath),
                'width' => $dimensions['width'] ?? null,
                'height' => $dimensions['height'] ?? null,
            ];
        }

        return $thumbnails;
    }

    /**
     * Get thumbnail filename
     */
    private function getThumbnailFilename(string $filename, string $sizeName): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        return "{$name}_{$sizeName}.webp";
    }

    /**
     * Get image URL
     */
    public function getUrl(string $path): ?string
    {
        $disk = $this->disk();

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }

    /**
     * Optimize image
     */
    public function optimize(string $path, int $quality = 85): bool
    {
        $disk = $this->disk();

        if (! Storage::disk($disk)->exists($path)) {
            return false;
        }

        $image = Image::read(Storage::disk($disk)->get($path));
        $this->put($disk, $path, (string) $image->toWebp($quality));

        return true;
    }

    private function disk(): string
    {
        return 'public';
    }

    private function put(string $disk, string $path, string $contents): void
    {
        $stored = Storage::disk($disk)->put($path, $contents, ['visibility' => 'public']);

        if (! $stored) {
            throw new RuntimeException('Gagal mengunggah gambar ke storage. Periksa konfigurasi disk dan permission bucket.');
        }
    }
}
