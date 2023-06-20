<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    const NORM_ROUTES = ['users', 'products', 'categories', 'roles'];
    const AUTH_ROUTES = ['templates'];
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/users/profile';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            foreach (self::NORM_ROUTES as $route) {
                Route::middleware(['web', 'auth'])
                    ->group(base_path('routes/' . $route . '.php'));
            }

            foreach (self::AUTH_ROUTES as $route) {
                Route::middleware(['web'])
                    ->group(base_path('routes/' . $route . '.php'));
            }
        });
    }
}
