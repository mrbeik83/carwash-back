<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::post('/login/request-otp', [AuthenticatedSessionController::class, 'requestOtp'])
        ->middleware('throttle:6,1')
        ->name('login.otp.request');

    Route::post('/login/verify-otp', [AuthenticatedSessionController::class, 'verifyOtp'])
        ->middleware('throttle:10,1')
        ->name('login.otp.verify');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', [AuthenticatedSessionController::class, 'home'])
        ->name('account.home');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/invitations/{token}', [InvitationController::class, 'show'])
        ->name('invitations.show');

    Route::post('/invitations/{token}', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
});

require __DIR__.'/admin.php';
require __DIR__.'/carwash.php';
