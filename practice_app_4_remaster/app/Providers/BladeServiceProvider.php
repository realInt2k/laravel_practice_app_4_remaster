<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::if('debug', function () {
            return config('constants.FRONT_END_DEBUG');
        });

        Blade::if('canManipulateUser', function (string $action, User | null $user = null) {
            return auth()->canPerformAction($action, $user);
        });

        Blade::if('hasRole', function (string $roleName) {
            return auth()->user()->hasRole($roleName);
        });

        Blade::if('hasPermission', function (string $permissionName) {
            return auth()->user()->hasPermission($permissionName);
        });
    }
}
