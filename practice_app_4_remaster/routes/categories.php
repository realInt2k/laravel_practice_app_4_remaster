<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;

Route::prefix('/categories')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [CategoryController::class, 'index'])
            ->name('categories.search');

        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/{id}', [CategoryController::class, 'show'])
            ->name('categories.show');

        Route::get('/{id}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit')
            ->middleware('categoryRoutePermission:p_categories-update');

        Route::get('/create', [CategoryController::class, 'create'])
            ->name('categories.create')
            ->middleware('categoryRoutePermission:p_categories-store');

        Route::delete('/{id}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy')
            ->middleware('categoryRoutePermission:p_categories-destroy');

        Route::delete('/{id}/ajax', [CategoryController::class, 'destroyAjax'])
            ->name('categories.destroy.ajax')
            ->middleware('categoryRoutePermission:p_categories-destroy');

        Route::put('/{id}', [CategoryController::class, 'update'])
            ->name('categories.update')
            ->middleware('categoryRoutePermission:p_categories-update');

        Route::put('/{id}/ajax/validation', [CategoryController::class, 'updateAjaxValidation'])
            ->name('categories.update.ajax.validation')
            ->middleware('categoryRoutePermission:p_categories-update');

        Route::put('/{id}/ajax', [CategoryController::class, 'updateAjax'])
            ->name('categories.update.ajax')
            ->middleware('categoryRoutePermission:p_categories-update');

        Route::post('/ajax/validation', [CategoryController::class, 'storeAjaxValidation'])
            ->name('categories.store.ajax.validation')
            ->middleware('categoryRoutePermission:p_categories-store');

        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store')
            ->middleware('categoryRoutePermission:p_categories-store');
    });
});