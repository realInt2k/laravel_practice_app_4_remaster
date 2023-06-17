<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('/users')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [UserController::class, 'search'])
            ->name('users.search');

        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::get('/{id}', [UserController::class, 'show'])
            ->name('users.show');

        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/{id}/ajax/validate', [UserController::class, 'updateAjaxValidation'])
            ->name('users.update.ajax.validation');

        Route::put('/{id}/ajax', [UserController::class, 'updateAjax'])
            ->name('users.update.ajax');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store');

        Route::post('/ajax/validate', [UserController::class, 'storeAjaxValidation'])
            ->name('users.store.ajax.validation');

        Route::put('/{id}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('users.destroy');

        Route::delete('/{id}/ajax', [UserController::class, 'destroyAjax'])
            ->name('users.destroy.ajax');
    });
});