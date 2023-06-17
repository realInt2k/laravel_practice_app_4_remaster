<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;

Route::prefix('/products')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [ProductController::class, 'index'])
            ->name('products.search');

        Route::get('/{id}/edit', [ProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware('productRoutePermission:p_products-update');

        Route::put('/{id}/ajax/validate', [ProductController::class, 'updateAjaxValidation'])
            ->name('products.update.ajax.validation')
            ->middleware('productRoutePermission:p_products-update');

        Route::put('/{id}/ajax/', [ProductController::class, 'updateAjax'])
            ->name('products.update.ajax')
            ->middleware('productRoutePermission:p_products-update');

        Route::post('/', [ProductController::class, 'store'])
            ->name('products.store')
            ->middleware('productRoutePermission:p_products-store');

        Route::post('/ajax/validate', [ProductController::class, 'storeAjaxValidation'])
            ->name('products.store.ajax.validation')
            ->middleware('permission:p_products-store');

        Route::get('/create', [ProductController::class, 'create'])
            ->name('products.create')
            ->middleware('permission:p_products-store');

        Route::put('/{id}', [ProductController::class, 'update'])
            ->name('products.update')
            ->middleware('productRoutePermission:p_products-update');

        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware('productRoutePermission:p_products-destroy');

        Route::delete('/{id}/ajax', [ProductController::class, 'destroyAjax'])
            ->name('products.destroy.ajax')
            ->middleware('productRoutePermission:p_products-destroy');

        Route::get('/', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/{id}', [ProductController::class, 'show'])
            ->name('products.show');
    });
});
