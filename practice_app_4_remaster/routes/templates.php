<?php

namespace App\Routes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;

Route::get('/', function () {
    return redirect('sign-in');
})->middleware('guest');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
    return view('sessions.password.verify');
})->middleware('guest')->name('verify');
Route::get('/reset-password/{token}', function ($token) {
    return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');
