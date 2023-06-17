<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('/products')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [ProductController::class, 'search'])
            ->name('products.search');

        Route::get('/{id}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/{id}/ajax/validate', [ProductController::class, 'updateAjaxValidation'])
            ->name('products.update.ajax.validation');

        Route::put('/{id}/ajax/', [ProductController::class, 'updateAjax'])
            ->name('products.update.ajax');

        Route::post('/', [ProductController::class, 'store'])
            ->name('products.store');

        Route::post('/ajax/validate', [ProductController::class, 'storeAjaxValidation'])
            ->name('products.store.ajax.validation');

        Route::get('/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::put('/{id}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->name('products.destroy');

        Route::delete('/{id}/ajax', [ProductController::class, 'destroyAjax'])
            ->name('products.destroy.ajax');

        Route::get('/', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/{id}', [ProductController::class, 'show'])
            ->name('products.show');
    });
});
