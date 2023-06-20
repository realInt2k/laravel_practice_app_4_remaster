<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('/users')->group(function () {
    
    Route::put('/profile', [UserController::class, 'updateProfile'])
        ->name('users.profile.update');

    Route::get('/profile', function () {
        return view('pages.users.user-profile');
    })->name('users.profile');

    Route::middleware('permissionCheck:r_admin')->group(function () {
        Route::get('/search', [UserController::class, 'search'])
            ->name('users.search');

        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/create', [UserController::class, 'create'])
            ->name('users.create')
            ->middleware('permissionCheck:p_users-store');

        Route::get('/{id}', [UserController::class, 'show'])
            ->name('users.show');

        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->name('users.edit')
            ->middleware('permissionCheck:p_users-update');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('permissionCheck:p_users-store');

        Route::put('/{id}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('permissionCheck:p_users-update');

        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('permissionCheck:p_users-destroy');
    });
});
