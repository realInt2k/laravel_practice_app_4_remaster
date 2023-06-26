<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;

trait ProcessImageTrait
{
    public function verify($request): bool
    {
        return isset($request->image);
    }

    public function saveFile($request): ?string
    {
        if ($this->verify($request)) {
            $dir = $this->createPublicDirIfNotExist();
            $name = $this->nameTheImage($request);
            $image = Image::make($request->file('image'));
            if (!file_exists($dir . $name)) {
                $image->save($dir . $name);
            }
            return $name;
        }
        return null;
    }

    public function deleteFile($name): void
    {
        if (isset($name)) {
            $this->removeFileFromPublicStorage(config('custom.constants.IMAGE_DIR') . $name);
        }
    }

    public function updateFile(Request $request, string|null $oldImage = null, bool $dry = false): ?string
    {
        if ($this->verify($request)) {
            if (!$dry) {
                $this->deleteFile($oldImage);
            }
            return $this->saveFile($request);
        } elseif (isset($request->remove_image_request) && $request->remove_image_request == "true") {
            if (!$dry) {
                $this->deleteFile($oldImage);
            }
            return null;
        } else {
            return $oldImage;
        }
    }

    public function nameTheImage($request): string
    {
        return pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME)
            . '_'
            . hrtime(true)
            . '.'
            . $request->image->extension();
    }

    protected function removeFileFromPublicStorage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    protected function createPublicDirIfNotExist(string $dir = 'images/'): string
    {
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }
        return Storage::disk('public')->path($dir);
    }

    /**
     * @param InterventionImage $image
     * @param int|null $width
     * @param int|null $height
     * @param bool $retainAspectRatio
     */
    public function resizeImage(InterventionImage &$image,
                                int|null          $width,
                                int|null          $height,
                                bool              $retainAspectRatio = true): void
    {
        if ($retainAspectRatio) {
            $image->resize(null, $width, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image->resize(null, $width, $height);
        }
    }
}
