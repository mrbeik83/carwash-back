<?php

use App\Enums\PermissionName;
use App\Http\Controllers\CarWashPanel\BookingController;
use App\Http\Controllers\CarWashPanel\CustomerController;
use App\Http\Controllers\CarWashPanel\DashboardController;
use App\Http\Controllers\CarWashPanel\MemberController;
use App\Http\Controllers\CarWashPanel\PaymentController;
use App\Http\Controllers\CarWashPanel\ProfileController;
use App\Http\Controllers\CarWashPanel\QrLinkController;
use App\Http\Controllers\CarWashPanel\ReportController;
use App\Http\Controllers\CarWashPanel\ScheduleController;
use App\Http\Controllers\CarWashPanel\ServiceController;
use App\Http\Controllers\CarWashPanel\SettingsController;
use App\Models\CarWash;
use Illuminate\Support\Facades\Route;

Route::prefix('carwash/{carWash:slug}')
    ->name('carwash.')
    ->middleware([
        'auth',
        'carwash.context',
        'carwash.member',
        'can:'.PermissionName::CAR_WASH_PANEL_ACCESS->value,
    ])
    ->scopeBindings()
    ->group(function (): void {
        Route::get('/', fn (CarWash $carWash) => redirect()->route('carwash.dashboard', $carWash))->name('root');

        Route::get('/dashboard', DashboardController::class)
            ->middleware('can:'.PermissionName::CAR_WASH_DASHBOARD_VIEW->value)
            ->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->middleware('can:'.PermissionName::CAR_WASH_PROFILE_VIEW->value)
            ->name('profile.edit');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->middleware('can:'.PermissionName::CAR_WASH_PROFILE_UPDATE->value)
            ->name('profile.update');

        Route::get('/settings', [SettingsController::class, 'edit'])
            ->middleware('can:'.PermissionName::CAR_WASH_SETTINGS_VIEW->value)
            ->name('settings.edit');

        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('can:'.PermissionName::CAR_WASH_SETTINGS_UPDATE->value)
            ->name('settings.update');

        Route::get('/members', [MemberController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_MEMBERS_VIEW->value)
            ->name('members.index');

        Route::post('/members/invite', [MemberController::class, 'invite'])
            ->middleware('can:'.PermissionName::CAR_WASH_MEMBERS_INVITE->value)
            ->name('members.invite');

        Route::put('/members/{member}/role', [MemberController::class, 'updateRole'])
            ->middleware('can:'.PermissionName::CAR_WASH_MEMBERS_UPDATE->value)
            ->name('members.role');

        Route::delete('/members/{member}', [MemberController::class, 'destroy'])
            ->middleware('can:'.PermissionName::CAR_WASH_MEMBERS_REMOVE->value)
            ->name('members.destroy');

        Route::get('/services', [ServiceController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_SERVICES_VIEW->value)
            ->name('services.index');

        Route::post('/services', [ServiceController::class, 'store'])
            ->middleware('can:'.PermissionName::CAR_WASH_SERVICES_CREATE->value)
            ->name('services.store');

        Route::put('/services/{service}', [ServiceController::class, 'update'])
            ->middleware('can:'.PermissionName::CAR_WASH_SERVICES_UPDATE->value)
            ->name('services.update');

        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])
            ->middleware('can:'.PermissionName::CAR_WASH_SERVICES_DELETE->value)
            ->name('services.destroy');

        Route::get('/schedule', [ScheduleController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_VIEW->value)
            ->name('schedule.index');

        Route::post('/schedule/weekly', [ScheduleController::class, 'saveWeekly'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.weekly.save');

        Route::post('/schedule/rules', [ScheduleController::class, 'storeRule'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.rules.store');

        Route::delete('/schedule/rules/{capacityRule}', [ScheduleController::class, 'destroyRule'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.rules.destroy');

        Route::post('/schedule/exceptions', [ScheduleController::class, 'storeException'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.exceptions.store');

        Route::delete('/schedule/exceptions/{scheduleException}', [ScheduleController::class, 'destroyException'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.exceptions.destroy');

        Route::put('/schedule/slots/{bookingSlot}', [ScheduleController::class, 'updateSlot'])
            ->middleware('can:'.PermissionName::CAR_WASH_SCHEDULE_MANAGE->value)
            ->name('schedule.slots.update');

        Route::post('/schedule/regenerate', [ScheduleController::class, 'regenerate'])
            ->middleware('can:'.PermissionName::CAR_WASH_SLOTS_REGENERATE->value)
            ->name('schedule.regenerate');

        Route::get('/bookings', [BookingController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_BOOKINGS_VIEW->value)
            ->name('bookings.index');

        Route::post('/bookings', [BookingController::class, 'store'])
            ->middleware('can:'.PermissionName::CAR_WASH_BOOKINGS_CREATE->value)
            ->name('bookings.store');

        Route::get('/bookings/{booking:public_id}', [BookingController::class, 'show'])
            ->middleware('can:'.PermissionName::CAR_WASH_BOOKINGS_VIEW->value)
            ->name('bookings.show');

        // Authorization is transition-specific inside the controller.
        // A generic bookings.update middleware would incorrectly block operators
        // who only have check-in/start/complete permissions.
        Route::post('/bookings/{booking:public_id}/transition', [BookingController::class, 'transition'])
            ->name('bookings.transition');

        Route::get('/customers', [CustomerController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_CUSTOMERS_VIEW->value)
            ->name('customers.index');

        Route::get('/reports', [ReportController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_REPORTS_VIEW->value)
            ->name('reports.index');

        Route::get('/qr-links', [QrLinkController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_QR_VIEW->value)
            ->name('qr.index');

        Route::post('/qr-links', [QrLinkController::class, 'store'])
            ->middleware('can:'.PermissionName::CAR_WASH_QR_MANAGE->value)
            ->name('qr.store');

        Route::delete('/qr-links/{qrLink}', [QrLinkController::class, 'destroy'])
            ->middleware('can:'.PermissionName::CAR_WASH_QR_MANAGE->value)
            ->name('qr.destroy');

        Route::get('/payments', [PaymentController::class, 'index'])
            ->middleware('can:'.PermissionName::CAR_WASH_PAYMENTS_VIEW->value)
            ->name('payments.index');

        Route::post('/bookings/{booking:public_id}/payments', [PaymentController::class, 'store'])
            ->middleware('can:'.PermissionName::CAR_WASH_PAYMENTS_CREATE->value)
            ->name('payments.store');
    });
