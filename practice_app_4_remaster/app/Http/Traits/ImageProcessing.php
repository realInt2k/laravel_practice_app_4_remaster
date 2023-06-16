<?php

namespace App\Http\Traits;

use Intervention\Image\Facades\Image;

trait ImageProcessing
{
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
