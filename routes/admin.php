<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CarWashController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('root');

        Route::get('/dashboard', DashboardController::class)
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

        Route::get('/bookings', [BookingController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_BOOKINGS_VIEW->value)
            ->name('bookings.index');

        Route::get('/finance', [FinanceController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_FINANCE_VIEW->value)
            ->name('finance.index');

        Route::get('/reports', [ReportController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_REPORTS_VIEW->value)
            ->name('reports.index');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_ROLES_MANAGE->value)
            ->name('roles.index');

        Route::get('/settings', [SystemSettingController::class, 'edit'])
            ->middleware('can:'.PermissionName::PLATFORM_SETTINGS_MANAGE->value)
            ->name('settings.index');

        Route::put('/settings', [SystemSettingController::class, 'update'])
            ->middleware('can:'.PermissionName::PLATFORM_SETTINGS_MANAGE->value)
            ->name('settings.update');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->middleware('can:'.PermissionName::PLATFORM_AUDIT_VIEW->value)
            ->name('audit-logs.index');
    });
