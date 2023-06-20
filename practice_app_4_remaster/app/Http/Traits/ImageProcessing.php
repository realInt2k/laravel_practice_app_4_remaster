<?php

namespace App\Http\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait ImageProcessing
{
    const PUBLIC_DIR = 'storage/images/';

    public function verify($request)
    {
        return isset($request->image);
    }

    public function saveFile($request)
    {
        if ($this->verify($request)) {
            $path = $this->createPublicDirIfNotExist();
            $name = $this->nameTheImage($request);
            $image = $this->makeImage($request->file('image'));
            if (!file_exists($path . $name)) {
                $this->saveImage($image, $path . $name);
            }
            return $name;
        }
        return null;
    }

    public function deleteFile(string $name)
    {
        if (isset($name)) {
            $this->removeFileFromPublicStorage(self::PUBLIC_DIR . $name);
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
            return null;
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

    protected function createPublicDirIfNotExist($dir = self::PUBLIC_DIR): string
    {
        $path = public_path($dir);
        !is_dir($path) &&
            mkdir($path, 0777, true);
        return $path;
    }

    public function makeImage($image)
    {
        return Image::make($image);
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

    /**
     * @param \Intervention\Image\Image $image 
     */
    public function saveImage(&$image, $path)
    {
        $image->save($path);
    }
}
