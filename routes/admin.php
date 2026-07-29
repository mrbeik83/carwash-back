<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Admin\CarWashController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'can:'.PermissionName::PLATFORM_ACCESS->value,
    ])
    ->group(function (): void {
        Route::get('/', DashboardController::class)
            ->middleware('can:'.PermissionName::PLATFORM_DASHBOARD_VIEW->value)
            ->name('dashboard');

        Route::get('/car-washes', [CarWashController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_VIEW->value)
            ->name('car-washes.index');

        Route::get('/car-washes/create', [CarWashController::class, 'create'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_CREATE->value)
            ->name('car-washes.create');

        Route::post('/car-washes', [CarWashController::class, 'store'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_CREATE->value)
            ->name('car-washes.store');

        Route::get('/car-washes/{carWash:slug}', [CarWashController::class, 'show'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_VIEW->value)
            ->name('car-washes.show');

        Route::get('/car-washes/{carWash:slug}/edit', [CarWashController::class, 'edit'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_UPDATE->value)
            ->name('car-washes.edit');

        Route::put('/car-washes/{carWash:slug}', [CarWashController::class, 'update'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_UPDATE->value)
            ->name('car-washes.update');

        Route::post('/car-washes/{carWash:slug}/status', [CarWashController::class, 'changeStatus'])
            ->middleware('can:'.PermissionName::PLATFORM_CAR_WASHES_APPROVE->value)
            ->name('car-washes.status');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_USERS_VIEW->value)
            ->name('users.index');

        Route::view('/bookings', 'admin.bookings.index')
            ->middleware('can:'.PermissionName::PLATFORM_BOOKINGS_VIEW->value)
            ->name('bookings.index');

        Route::view('/finance', 'admin.finance.index')
            ->middleware('can:'.PermissionName::PLATFORM_FINANCE_VIEW->value)
            ->name('finance.index');

        Route::view('/reports', 'admin.reports.index')
            ->middleware('can:'.PermissionName::PLATFORM_REPORTS_VIEW->value)
            ->name('reports.index');

        Route::view('/roles', 'admin.roles.index')
            ->middleware('can:'.PermissionName::PLATFORM_ROLES_MANAGE->value)
            ->name('roles.index');

        Route::view('/settings', 'admin.settings.index')
            ->middleware('can:'.PermissionName::PLATFORM_SETTINGS_MANAGE->value)
            ->name('settings.index');

        Route::view('/audit-logs', 'admin.audit-logs.index')
            ->middleware('can:'.PermissionName::PLATFORM_AUDIT_VIEW->value)
            ->name('audit-logs.index');
    });
