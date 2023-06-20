<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('/products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->name('products.index');

    Route::post('/', [ProductController::class, 'store'])
        ->name('products.store')
        ->middleware('check.permission:products-store');

    Route::get('/search', [ProductController::class, 'search'])
        ->name('products.search');

    Route::get('/create', [ProductController::class, 'create'])
        ->name('products.create')
        ->middleware('check.permission:products-store');

    Route::get('/{id}', [ProductController::class, 'show'])
        ->name('products.show');

    Route::put('/{id}', [ProductController::class, 'update'])
        ->name('products.update')
        ->middleware('check.permission:products-update');

    Route::delete('/{id}', [ProductController::class, 'destroy'])
        ->name('products.destroy')
        ->middleware('check.permission:products-destroy');

    Route::get('/{id}/edit', [ProductController::class, 'edit'])
        ->name('products.edit')
        ->middleware('check.permission:products-update');
});
