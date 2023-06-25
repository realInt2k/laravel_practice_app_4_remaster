<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
 * Backend Routes
 *
 * These routes can only be accessed by authenticated users
 */

Route::group(['middleware' => 'auth'], function () {
    includeRouteFiles(__DIR__ . '/backend/');
});

/*
 * Frontend Routes
 *
 * These routes validates users, or let them signup
 */
includeRouteFiles(__DIR__ . '/frontend/');
