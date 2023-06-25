<?php

namespace App\Http\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait ProcessImageTrait
{
    public function verify($request)
    {
        return isset($request->image);
    }

    public function saveFile($request)
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

    public function deleteFile($name)
    {
        if (isset($name)) {
            $this->removeFileFromPublicStorage(config('custom.constants.IMAGE_DIR') . $name);
        }
    }

    public function updateFile($request, $oldImage = null)
    {
        if ($this->verify($request)) {
            if (isset($oldImage)) {
                $this->deleteFile($oldImage);
            }
            $image = $this->saveFile($request);
            return $image;
        } else {
            return (isset($request->remove_image_request) && $request->remove_image_request == "true") ?
                null : $oldImage;
        }
    }

    public function nameTheImage($request)
    {
        return pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME)
            . '_'
            . hrtime(true)
            . '.'
            . $request->image->extension();
    }

    protected function removeFileFromPublicStorage($path)
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
     * @param \Intervention\Image\Image $image
     */
    public function resizeImage(&$image, $width, $height, bool $retainAspectRatio = true)
    {
        if ($retainAspectRatio) {
            $image->resize(null, $width, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image->resize(null, $width, $height);
        }
    }

    /**
     * @param \Intervention\Image\Image $image
     */
    public function cropImage(&$image, $width, $height, $x = null, $y = null)
    {
        return $image->crop($width, $height, $x, $y);
    }

    /**
     * @param \Intervention\Image\Image $image
     */
    public function blurImage(&$image, $intensity = 1)
    {
        if (abs($intensity) > 100) {
            return;
        }
        $image->blur($intensity);
    }

    /**
     * @param \Intervention\Image\Image $image
     */
    public function brighten(&$image, $intensity = 0)
    {
        if (abs($intensity) > 100) {
            return;
        }
        $image->brightness($intensity);
    }
}
