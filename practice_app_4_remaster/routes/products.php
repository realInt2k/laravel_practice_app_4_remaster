<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('/products')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [ProductController::class, 'search'])
            ->name('products.search');

        Route::get('/{id}/edit', [ProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware('permissionCheck:p_products-update');

        Route::post('/', [ProductController::class, 'store'])
            ->name('products.store')
            ->middleware('permissionCheck:p_products-store');

        Route::get('/create', [ProductController::class, 'create'])
            ->name('products.create')
            ->middleware('permissionCheck:p_products-store');

        Route::put('/{id}', [ProductController::class, 'update'])
            ->name('products.update')
            ->middleware('permissionCheck:p_products-update');

        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware('permissionCheck:p_products-destroy');

        Route::get('/', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/{id}', [ProductController::class, 'show'])
            ->name('products.show');
    });
});
