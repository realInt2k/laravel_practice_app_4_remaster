<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::prefix('/categories')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [CategoryController::class, 'search'])
            ->name('categories.search');

        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/{id}', [CategoryController::class, 'show'])
            ->name('categories.show');

        Route::get('/{id}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::get('/create', [CategoryController::class, 'create'])
            ->name('categories.create');

        Route::delete('/{id}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        Route::delete('/{id}/ajax', [CategoryController::class, 'destroyAjax'])
            ->name('categories.destroy.ajax');

        Route::put('/{id}', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::put('/{id}/ajax/validation', [CategoryController::class, 'updateAjaxValidation'])
            ->name('categories.update.ajax.validation');

        Route::put('/{id}/ajax', [CategoryController::class, 'updateAjax'])
            ->name('categories.update.ajax');

        Route::post('/ajax/validation', [CategoryController::class, 'storeAjaxValidation'])
            ->name('categories.store.ajax.validation');

        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');
    });
});