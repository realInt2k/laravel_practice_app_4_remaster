<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;


Route::prefix('/roles')->group(function () {
    Route::middleware('auth')->middleware('permissionCheck:r_super-admin|r_admin')
        ->group(function () {
            Route::get('/search', [RoleController::class, 'search'])
                ->name('roles.search');

            Route::get('/create', [RoleController::class, 'create'])
                ->name('roles.create')
                ->middleware('permissionCheck:p_roles-store');

            Route::get('/', [RoleController::class, 'index'])
                ->name('roles.index');

            Route::get('/{id}', [RoleController::class, 'show'])
                ->name('roles.show');

            Route::get('/{id}/edit', [RoleController::class, 'edit'])
                ->name('roles.edit')
                ->middleware('permissionCheck:p_roles-update');

            Route::post('/', [RoleController::class, 'store'])
                ->name('roles.store')
                ->middleware('permissionCheck:p_roles-store');

            Route::put('/{id}', [RoleController::class, 'update'])
                ->name('roles.update')
                ->middleware('permissionCheck:p_roles-update');

            Route::delete('/{id}', [RoleController::class, 'destroy'])
                ->name('roles.destroy')
                ->middleware('permissionCheck:p_roles-destroy');

        });
});
