<?php

use App\Http\Controllers\Auth\CarWashAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('carwash')->name('carwash.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [CarWashAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('/login', [CarWashAuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('login.store');

        Route::post('/login/request-otp', [CarWashAuthenticatedSessionController::class, 'requestOtp'])
            ->middleware('throttle:6,1')
            ->name('login.otp.request');

        Route::post('/login/verify-otp', [CarWashAuthenticatedSessionController::class, 'verifyOtp'])
            ->middleware('throttle:10,1')
            ->name('login.otp.verify');
    });

    Route::get('/select', [CarWashAuthenticatedSessionController::class, 'select'])
        ->middleware('auth')
        ->name('select');

    Route::post('/logout', [CarWashAuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});
