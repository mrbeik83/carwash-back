<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function requestOtp(): RedirectResponse
    {
        return redirect()->route('carwash.login');
    }

    public function verifyOtp(): RedirectResponse
    {
        return redirect()->route('carwash.login');
    }

    public function home(Request $request): RedirectResponse
    {
        if ($request->user()?->is_super_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('carwash.select');
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
