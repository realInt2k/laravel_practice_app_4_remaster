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

    Route::get('/search', [UserController::class, 'search'])
        ->name('users.search');

    Route::get('/', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('check.permission:users.store');

    Route::get('/{id}', [UserController::class, 'show'])
        ->name('users.show');

    Route::post('/', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('check.permission:users.store');

    Route::get('/{id}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('check.permission:users.update')
        ->middleware('protect.admin:users.update');

    Route::put('/{id}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('check.permission:users.update')
        ->middleware('protect.admin:users.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('check.permission:users.destroy')
        ->middleware('protect.admin:users.destroy');
});
