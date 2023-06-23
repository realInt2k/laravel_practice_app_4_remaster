<?php

namespace App\Helpers;

use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class RouteHelper
{
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    static function includeFilesInFolder($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            /** @var RecursiveDirectoryIterator */
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    /**
     * @param $folder
     */
    static function includeRouteFiles($folder)
    {
        self::includeFilesInFolder($folder);
    }
}
