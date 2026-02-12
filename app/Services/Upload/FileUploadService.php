<?php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed file types
     */
    private array $allowedTypes = [
        'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'videos' => ['mp4', 'avi', 'mov', 'wmv'],
        'archives' => ['zip', 'rar', '7z', 'tar', 'gz'],
    ];

    /**
     * Upload file
     */
    public function upload(
        UploadedFile $file,
        string $directory = 'files',
        ?string $customFilename = null
    ): array {
        $filename = $customFilename ?? $this->generateFilename($file);
        $disk = config('filesystems.default', 'public');

        // Validate file type
        if (!$this->validateFileType($file)) {
            throw new \Exception('File type not allowed');
        }

        // Upload file
        $path = $file->storeAs($directory, $filename, $disk);

        return [
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    /**
     * Delete file
     */
    public function delete(string $path): bool
    {
        $disk = config('filesystems.default', 'public');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '_' . time();

        return "{$filename}.{$extension}";
    }

    /**
     * Validate file type
     */
    private function validateFileType(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        foreach ($this->allowedTypes as $types) {
            if (in_array($extension, $types)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get file URL
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
     * Get file size in human readable format
     */
    public function getHumanReadableSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}