<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;


Route::prefix('/roles')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/search', [RoleController::class, 'index'])
            ->name('roles.search');

        Route::get('/create', [RoleController::class, 'create'])
            ->name('roles.create')->middleware('roleRoutePermission:p_roles-store');

        Route::get('/', [RoleController::class, 'index'])
            ->name('roles.index');

        Route::get('/{id}', [RoleController::class, 'show'])
            ->name('roles.show');

        Route::get('/{id}/edit', [RoleController::class, 'edit'])
            ->name('roles.edit')
            ->middleware('roleRoutePermission:p_roles-update');

        Route::put('/{id}/ajax/validate', [RoleController::class, 'updateAjaxValidation'])
            ->name('roles.update.ajax.validation')
            ->middleware('roleRoutePermission:p_roles-update');

        Route::put('/{id}/ajax', [RoleController::class, 'updateAjax'])
            ->name('roles.update.ajax')
            ->middleware('roleRoutePermission:p_roles-update');

        Route::post('/', [RoleController::class, 'store'])
            ->name('roles.store')
            ->middleware('roleRoutePermission:p_roles-store');

        Route::post('/ajax/validate', [RoleController::class, 'storeAjaxValidation'])
            ->name('roles.store.ajax.validation')
            ->middleware('roleRoutePermission:p_roles-store');

        Route::put('/{id}', [RoleController::class, 'update'])
            ->name('roles.update')
            ->middleware('roleRoutePermission:p_roles-update');

        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->name('roles.destroy')
            ->middleware('roleRoutePermission:p_roles-destroy');

        Route::delete('/{id}/ajax', [RoleController::class, 'destroyAjax'])
            ->name('roles.destroy.ajax')
            ->middleware('roleRoutePermission:p_roles-destroy');
    });
});
