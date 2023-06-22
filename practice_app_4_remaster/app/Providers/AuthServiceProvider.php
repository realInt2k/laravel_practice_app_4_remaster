<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        SessionGuard::macro('canPerformAction', function ($action, User | null $user = null) {
            /** @var User */
            $authUser = auth()->user();
            if ($authUser->isSuperAdmin()) {
                return true;
            } elseif ($authUser->isAdmin()) {
                if ($user != null && $user->isSuperAdmin()) {
                    return false;
                } else {
                    return $authUser->hasPermission($action);
                }
            } else {
                if ($user != null && ($user->isSuperAdmin() || $user->isAdmin())) {
                    return false;
                } else {
                    return $authUser->hasPermission($action);
                }
            }
        });
    }
}
