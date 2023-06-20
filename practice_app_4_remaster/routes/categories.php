<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::prefix('/categories')->group(function () {
    Route::middleware('permissionCheck:r_admin')->group(function () {
        Route::get('/search', [CategoryController::class, 'search'])
            ->name('categories.search');

        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/{id}', [CategoryController::class, 'show'])
            ->name('categories.show');

        Route::get('/{id}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit')
            ->middleware('permissionCheck:p_categories-edit');

        Route::get('/create', [CategoryController::class, 'create'])
            ->name('categories.create')
            ->middleware('permissionCheck:p_categories-store');

        Route::delete('/{id}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy')
            ->middleware('permissionCheck:p_categories-destroy');

        Route::put('/{id}', [CategoryController::class, 'update'])
            ->name('categories.update')
            ->middleware('permissionCheck:p_categories-update');

        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store')
            ->middleware('permissionCheck:p_categories-store');
    });
});
