<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('/products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->name('products.index');

    Route::post('/', [ProductController::class, 'store'])
        ->name('products.store')
        ->middleware('check.permission_or_role:products-store');

    Route::get('/search', [ProductController::class, 'search'])
        ->name('products.search');

    Route::get('/create', [ProductController::class, 'create'])
        ->name('products.create')
        ->middleware('check.permission_or_role:products-store');

    Route::middleware('productCheck')->group(function () {
        Route::get('/{id}', [ProductController::class, 'show'])
            ->name('products.show');

        Route::put('/{id}', [ProductController::class, 'update'])
            ->name('products.update')
            ->middleware('check.permission_or_role:products-update');

        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware('check.permission_or_role:products-destroy');

        Route::get('/{id}/edit', [ProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware('check.permission_or_role:products-update');
    });
});
