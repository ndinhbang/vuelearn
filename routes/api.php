<?php

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
        Route::post('login', \App\Http\Controllers\Auth\LoginController::class)->name('login')->middleware(['throttle:5,5']);
        Route::post('refresh', \App\Http\Controllers\Auth\RefreshController::class)->name('refresh')->middleware(['throttle:5,15']);

        Route::middleware(['auth:api'])->group(function () {
            Route::delete('logout', \App\Http\Controllers\Auth\LogoutController::class)->name('logout');
            Route::get('user', \App\Http\Controllers\Auth\UserController::class)->name('user');
        });
    });

    // Authenticated Routes
    Route::middleware(['auth:api'])->group(function () {
//        Route::prefix("articles")->name('articles.')->group(function () {
//            Route::get('/', \App\Http\Controllers\Article\Browse::class)->name('index');
//            Route::get('/{article}', \App\Http\Controllers\Article\Show::class)->name('show');
//        });
    });
});
