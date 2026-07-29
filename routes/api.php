<?php

use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\PublicCarWashController;
use App\Http\Controllers\Api\V1\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::middleware('throttle:api')->group(function (): void {
            Route::get('/car-washes/{carWash:slug}', [PublicCarWashController::class, 'show'])
                ->name('car-washes.show');

            Route::get('/car-washes/{carWash:slug}/availability', [PublicCarWashController::class, 'availability'])
                ->name('car-washes.availability');

            Route::post('/auth/request-otp', [OtpController::class, 'request'])
                ->middleware('throttle:6,1')
                ->name('auth.request-otp');

            Route::post('/auth/verify-otp', [OtpController::class, 'verify'])
                ->middleware('throttle:10,1')
                ->name('auth.verify-otp');
        });

        Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
            Route::get('/me', MeController::class)
                ->name('me');

            Route::post('/auth/logout', [OtpController::class, 'logout'])
                ->name('auth.logout');

            Route::get('/vehicles', [VehicleController::class, 'index'])
                ->name('vehicles.index');

            Route::post('/vehicles', [VehicleController::class, 'store'])
                ->name('vehicles.store');

            Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])
                ->name('vehicles.destroy');

            Route::get('/bookings', [BookingController::class, 'index'])
                ->name('bookings.index');

            Route::post('/bookings', [BookingController::class, 'store'])
                ->name('bookings.store');

            Route::get('/bookings/{booking:public_id}', [BookingController::class, 'show'])
                ->name('bookings.show');

            Route::post('/bookings/{booking:public_id}/cancel', [BookingController::class, 'cancel'])
                ->name('bookings.cancel');
        });
    });
