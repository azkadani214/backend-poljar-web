<?php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

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
        $disk = config('filesystems.default', 'public');
        
        // Create directory if not exists
        if (!Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }

        $basePath = Storage::disk($disk)->path($directory);
        $fullPath = "{$basePath}/{$filename}";
        $relativeStoragePath = "{$directory}/{$filename}";

        // Process original image: Convert to WebP and resize if too large
        $image = Image::read($file->getRealPath());
        
        // Max width for "original" to prevent massive files
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        // Save as WebP
        $image->toWebp(80)->save($fullPath);

        $result = [
            'path' => $relativeStoragePath,
            'url' => Storage::disk($disk)->url($relativeStoragePath),
            'filename' => $filename,
            'size' => filesize($fullPath),
            'mime_type' => 'image/webp',
            'original_name' => $file->getClientOriginalName(),
        ];

        // Generate thumbnails if sizes provided
        if (!empty($sizes)) {
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
        $disk = config('filesystems.default', 'public');

        if (!Storage::disk($disk)->exists($path)) {
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
        $filename = Str::uuid() . '_' . time();

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
        $disk = config('filesystems.default', 'public');
        $basePath = Storage::disk($disk)->path($directory);

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

            // Save as WebP
            $image->toWebp(80)->save("{$basePath}/{$thumbnailFilename}");

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
        $disk = config('filesystems.default', 'public');

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
        $disk = config('filesystems.default', 'public');

        if (!Storage::disk($disk)->exists($path)) {
            return false;
        }

        $fullPath = Storage::disk($disk)->path($path);
        $image = Image::read($fullPath);
        $image->save($fullPath, $quality);

        return true;
    }
}