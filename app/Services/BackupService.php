<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupService
{
    private Filesystem $disk;

    public function __construct(string $diskName = 'backups')
    {
        $this->disk = Storage::disk($diskName);
    }

    public function getBackups()
    {
        $backups = collect();
        $dirs = $this->disk->directories();
        foreach ($dirs as $dir) {
            $files = $this->disk->files($dir);
            $files = collect($files)->map(function ($file) {
                return (object)[
                    'dir' => explode('/', $file)[0],
                    'file' => explode('/', $file)[1]
                ];
            });
            $backups = $backups->merge($files);
        }

        return $backups->sortBy(fn($backup) => $backup->file);
    }

    public function download($filename): StreamedResponse
    {
        return $this->disk->download($filename);
    }

    public function delete($filename): bool
    {
        return $this->disk->delete($filename);
    }

    public function create(): int
    {
        return Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);
    }
}
