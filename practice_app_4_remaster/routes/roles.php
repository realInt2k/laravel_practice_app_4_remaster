<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;


Route::prefix('/roles')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [RoleController::class, 'search'])
            ->name('roles.search');

        Route::get('/create', [RoleController::class, 'create'])
            ->name('roles.create')->middleware('roleRoutePermission:p_roles-store');

        Route::get('/', [RoleController::class, 'index'])
            ->name('roles.index');

        Route::get('/{id}', [RoleController::class, 'show'])
            ->name('roles.show');

        Route::get('/{id}/edit', [RoleController::class, 'edit'])
            ->name('roles.edit');

        Route::put('/{id}/ajax/validate', [RoleController::class, 'updateAjaxValidation'])
            ->name('roles.update.ajax.validation');

        Route::put('/{id}/ajax', [RoleController::class, 'updateAjax'])
            ->name('roles.update.ajax');

        Route::post('/', [RoleController::class, 'store'])
            ->name('roles.store');

        Route::post('/ajax/validate', [RoleController::class, 'storeAjaxValidation'])
            ->name('roles.store.ajax.validation');

        Route::put('/{id}', [RoleController::class, 'update'])
            ->name('roles.update');

        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->name('roles.destroy');

        Route::delete('/{id}/ajax', [RoleController::class, 'destroyAjax'])
            ->name('roles.destroy.ajax');
    });
});
