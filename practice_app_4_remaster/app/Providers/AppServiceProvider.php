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
        Paginator::useBootstrap();

        Blade::if('adminOnly', function () {
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

        Blade::if('canManipulateRole', function (string $action, Role | null $role = null) {
            /** @var User */
            $authUser = auth()->user();
            if ($role) {
                if ($role->name == 'admin' || $role->name == 'super-admin') {
                    return $authUser->isSuperAdmin();
                }
            }
            return
                $authUser->isSuperAdmin() ||
                ($authUser->isAdmin() && $authUser->hasPermissionNames($action));
        });

        Blade::if('canManipulateProduct', function (string $action, Product | null $product = null) {
            /** @var User */
            $authUser = auth()->user();
            $authOwnProduct = $product ? $authUser->hasProduct($product->id) : true;
            return
                $authUser->isSuperAdmin() ||
                ($authUser->hasPermissionNames($action) && $authUser->isAdmin()) ||
                ($authUser->hasPermissionNames($action) && $authOwnProduct);
        });

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
