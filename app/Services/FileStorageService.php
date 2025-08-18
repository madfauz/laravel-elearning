<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileStorageService
{
    protected string $disk;

    public function __construct(string $disk = 'public')
    {
        $this->disk = $disk;
    }


    public function upload(UploadedFile $file, string $directory = ''): string
    {
        $extension = $file->getClientOriginalExtension();

        $fileName = uniqid() . '_' . time() . '.' . $extension;

        return $file->storeAs($directory, $fileName, $this->disk);
    }

    public function read(string $path): ?string
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->get($path);
        }
        return null;
    }

    public function update(string $oldPath, UploadedFile $newFile, string $directory = ''): string
    {
        $this->delete($oldPath);
        return $this->upload($newFile, $directory);
    }

    public function delete(string $path): bool
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }
        return false;
    }

    public function url(string $path): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $path = Storage::url($path);

        return asset($path);
    }
}
