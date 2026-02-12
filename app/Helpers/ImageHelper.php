<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageHelper
{
    /**
     * Upload image
     */
    public static function upload(
        UploadedFile $file,
        string $directory = 'images',
        string $disk = 'public',
        array $sizes = []
    ): array {
        $filename = self::generateFilename($file);
        $path = $file->storeAs($directory, $filename, $disk);

        $result = [
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'filename' => $filename,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];

        // Generate thumbnails if sizes provided
        if (!empty($sizes)) {
            $result['thumbnails'] = self::generateThumbnails($file, $directory, $filename, $disk, $sizes);
        }

        return $result;
    }

    /**
     * Delete image
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Generate unique filename
     */
    private static function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '_' . time();

        return "{$filename}.{$extension}";
    }

    /**
     * Generate thumbnails
     */
    private static function generateThumbnails(
        UploadedFile $file,
        string $directory,
        string $filename,
        string $disk,
        array $sizes
    ): array {
        $thumbnails = [];
        $basePath = Storage::disk($disk)->path($directory);

        foreach ($sizes as $sizeName => $dimensions) {
            $thumbnailFilename = self::getThumbnailFilename($filename, $sizeName);
            $thumbnailPath = "{$directory}/{$thumbnailFilename}";

            // Create thumbnail using Intervention Image
            $image = Image::read($file->getRealPath());
            
            if (isset($dimensions['width']) && isset($dimensions['height'])) {
                $image->cover($dimensions['width'], $dimensions['height']);
            } elseif (isset($dimensions['width'])) {
                $image->resize(width: $dimensions['width']);
            }

            $image->save("{$basePath}/{$thumbnailFilename}");

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
    private static function getThumbnailFilename(string $filename, string $sizeName): string
    {
        $parts = pathinfo($filename);
        return "{$parts['filename']}_{$sizeName}.{$parts['extension']}";
    }

    /**
     * Get image URL
     */
    public static function url(string $path, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }

    /**
     * Get default avatar URL
     */
    public static function defaultAvatar(string $name): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }
}
