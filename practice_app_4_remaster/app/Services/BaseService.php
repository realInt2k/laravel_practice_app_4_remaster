<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class BaseService
{
    protected function removeFileFromPublicStorage($path)
    {
        Storage::disk('public')->delete($path);
    }

    protected function createPublicDirIfNotExist($dir = 'storage'): string
    {
        $path = public_path($dir);
        !is_dir($path) &&
            mkdir($path, 0777, true);
        return $path;
    }

    protected function nameTheImage($request)
    {
        return pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME)
            . '_'
            . hrtime(true)
            . '.'
            . $request->image->extension();
    }
}
