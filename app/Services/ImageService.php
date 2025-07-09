<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;

class ImageService
{
    private Filesystem $disk;

    public function __construct(string $diskName = 'avatars')
    {
        $this->disk = Storage::disk($diskName);
    }

    public function saveImage(string $subFolder, UploadedFile $file): bool
    {
        $webpImage = $this->convertToWebp($file);

        return $this->disk->put("{$subFolder}/original.webp", $webpImage);
    }

    private function convertToWebp($file): EncodedImageInterface
    {
        $imageManager = ImageManager::gd(autoOrientation: true);

        return $imageManager->read($file)->scaleDown(1920,1920)->encode(new WebpEncoder());
    }

    public function getImageURL(string $subFolder, int $width = 300, int $height = 300): string
    {
        $nowTimestamp = time();
        if ($this->disk->exists("{$subFolder}/{$width}x{$height}.webp")) {
            return $this->disk->url("{$subFolder}/{$width}x{$height}.webp")."?t={$nowTimestamp}";
        }

        if ($this->disk->exists("{$subFolder}/original.webp")
            && $this->cropImage(
                $this->disk->get("{$subFolder}/original.webp"),
                "{$subFolder}/{$width}x{$height}.webp",
                $width,
                $height
            )) {
            return $this->disk->url("{$subFolder}/{$width}x{$height}.webp");
        }

        return $this->disk->url('default.webp');
    }

    private function cropImage($file, $name, int $width = 300, int $height = 300): bool
    {
        $imageManager = ImageManager::gd(autoOrientation: true);
        $image = $imageManager->read($file)
            ->cover($width, $height)
            ->encode(new WebpEncoder());

        return $this->disk->put($name, $image);
    }

    public function deleteDirectory(string $subFolder): void
    {
        if ($this->disk->exists($subFolder)) {
            $this->disk->deleteDirectory($subFolder);
        }
    }

    public function renameFolder(string $path, string $newPath): void
    {
        if ($this->disk->exists($path)) {
            $this->disk->move($path, $newPath);
        }
    }

    public function uploadAvatar(User $user, UploadedFile $file)
    {
        $this->renameFolder("{$user->id}/", "{$user->id}_old/");
        $res = $this->saveImage($user->id, $file);

        if ($res) {
            $this->deleteDirectory("{$user->id}_old/");
            return true;
        }else{
            $this->deleteDirectory("{$user->id}/");
            $this->renameFolder("{$user->id}_old/", "{$user->id}/");
            return false;
        }
    }
}
