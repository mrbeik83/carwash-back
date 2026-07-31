<?php

use App\Http\Controllers\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (! $request->user()) {
        return view('auth.portal');
    }

    if ($request->user()->is_super_admin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('carwash.select');
})->name('home');

Route::view('/login', 'auth.portal')->middleware('guest')->name('login');

require __DIR__.'/admin-auth.php';
require __DIR__.'/carwash-auth.php';

Route::middleware('auth')->group(function (): void {
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])
        ->name('invitations.show');

    Route::post('/invitations/{token}', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
});

require __DIR__.'/admin.php';
require __DIR__.'/carwash.php';
