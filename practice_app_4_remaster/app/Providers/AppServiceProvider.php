<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('canManipulateUser', function (string $action, User | null $user = null) {
            /** @var User */
            $authUser = auth()->user();
            if ($authUser->isSuperAdmin()) {
                return true;
            } else if ($authUser->isAdmin()) {
                $checkPermission = $authUser->hasPermissionNames($action);
                if (!$user) {
                    return $checkPermission;
                }
                if ($user->isSuperAdmin()) {
                    return false;
                } else if ($user->isAdmin()) {
                    return $checkPermission && $user->id === $authUser->id;
                } else {
                    return $checkPermission;
                }
            } else {
                return false;
            }
        });

        Blade::if('hasRole', function (string $roleName) {
            /** @var User */
            $authUser = auth()->user();
            return $authUser->hasRoleNames($roleName);
        });

        Blade::if('hasPermission', function (string $permissionName) {
            /** @var User */
            $authUser = auth()->user();
            return $authUser->hasPermissionNames($permissionName);
        });
    }
}
