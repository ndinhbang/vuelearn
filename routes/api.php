<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix("v1")->name('v1.')->group(function () {
    Route::prefix("auth")->name('auth.')->group(function () {
        Route::post('login', \App\Http\Controllers\Auth\LoginController::class)->name('login')->middleware(['throttle:5,1']);
        Route::post('logout', \App\Http\Controllers\Auth\LogoutController::class)->name('logout');
    });
});
