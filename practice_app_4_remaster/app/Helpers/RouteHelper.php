<?php
namespace App\Helpers;

use Illuminate\Support\Facades\File;

class RouteHelper
{
    public static function requireRoute(): void
    {
        foreach (File::allFiles(base_path('routes')) as $route_file) {
            (basename($route_file) === 'web.php') ?: (require $route_file);
        }
    }
}
