<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers;

Route::group(['as' => 'passport.', 'prefix' => config('passport.path', 'oauth')], function () {
    Route::post('/token', [Controllers\AccessTokenController::class, 'issueToken'])->name('token')->middleware('throttle');
    Route::get('/authorize', [Controllers\AuthorizationController::class, 'authorize'])->name('authorizations.authorize')->middleware('web');
    $guard = config('passport.guard');
    Route::middleware(['web', $guard ? 'auth:' . $guard : 'auth'])->group(function () {
        Route::post('/token/refresh', [Controllers\TransientTokenController::class, 'refresh'])->name('token.refresh');
        Route::post('/authorize', [Controllers\ApproveAuthorizationController::class, 'approve'])->name('authorizations.approve');
        Route::delete('/authorize', [Controllers\DenyAuthorizationController::class, 'deny'])->name('authorizations.deny');
        Route::get('/tokens', [Controllers\AuthorizedAccessTokenController::class, 'forUser'])->name('tokens.index');
        Route::delete('/tokens/{token_id}', [Controllers\AuthorizedAccessTokenController::class, 'destroy'])->name('tokens.destroy');
        Route::get('/clients', [Controllers\ClientController::class, 'forUser'])->name('clients.index');
        Route::post('/clients', [Controllers\ClientController::class, 'store'])->name('clients.store');
        Route::put('/clients/{client_id}', [Controllers\ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client_id}', [Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
        Route::get('/scopes', [Controllers\ScopeController::class, 'all'])->name('scopes.index');
//        Route::get('/personal-access-tokens', [Controllers\PersonalAccessTokenController::class, 'forUser'])->name('personal.tokens.index');
//        Route::post('/personal-access-tokens', [Controllers\PersonalAccessTokenController::class, 'store'])->name('personal.tokens.store');
//        Route::delete('/personal-access-tokens/{token_id}', [Controllers\PersonalAccessTokenController::class, 'destroy'])->name('personal.tokens.destroy');
    });
});
