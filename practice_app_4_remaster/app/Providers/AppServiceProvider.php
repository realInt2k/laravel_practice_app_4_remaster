<?php

namespace App\Providers;

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
        Paginator::useBootstrap();

        Blade::if('canSeeExtraInfo', function () {
            /** @var User */
            $authUser = auth()->user();
            return
                $authUser->isSuperAdmin() ||
                $authUser->isAdmin();
        });

        Blade::if('canManipulateCategory', function (string $action) {
            /** @var User */
            $authUser = auth()->user();
            return
                $authUser->isSuperAdmin() ||
                ($authUser->isAdmin() && $authUser->hasPermissionNames($action));
        });

        Blade::if('canManipulateRole', function (string $action, Role $role) {
            /** @var User */
            $authUser = auth()->user();
            if ($role->name == 'admin' || $role->name == 'super-admin') {
                return $authUser->isSuperAdmin();
            }
            return
                $authUser->isSuperAdmin() ||
                ($authUser->isAdmin() && $authUser->hasPermissionNames($action));
        });

        Blade::if('canManipulateProduct', function (string $action, Product $product) {
            /** @var User */
            $authUser = auth()->user();
            return
                $authUser->isSuperAdmin() ||
                ($authUser->hasPermissionNames($action) && $authUser->isAdmin()) ||
                ($authUser->hasPermissionNames($action) && $authUser->hasProduct($product->id));
        });

        Blade::if('canManipulateUser', function (string $action, User $user) {
            /** @var User */
            $authUser = auth()->user();
            if ($authUser->isSuperAdmin()) {
                return true;
            } else if ($authUser->isAdmin()) {
                $checkPermission = $authUser->hasPermissionNames($action);
                if ($user->isSuperAdmin()) {
                    return false;
                } else if ($user->isAdmin()) {
                    return $checkPermission && $user->id === $authUser->id;
                } else {
                    return $checkPermission;
                }
            } else {
                return $user->id === $authUser->id;
            }
        });

        Blade::if('hasRoleName', function (string $roleName) {
            /** @var User */
            $authUser = auth()->user();
            return $authUser->hasRoleNames($roleName);
        });

        Blade::if('allowedToChangeRoleAndPermission', function () {
            /** @var User */
            $authUser = auth()->user();
            return $authUser->isSuperAdmin();
        });
    }
}
