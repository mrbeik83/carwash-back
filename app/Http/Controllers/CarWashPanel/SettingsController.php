<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\UpdateSettingsRequest;
use App\Models\CarWash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(CarWash $carWash): View
    {
        $settings = $carWash->setting()->firstOrCreate([]);
        return view('carwash.settings.edit', compact('carWash', 'settings'));
    }

    public function update(UpdateSettingsRequest $request, CarWash $carWash): RedirectResponse
    {
        $carWash->setting()->updateOrCreate([], $request->validated());
        return back()->with('success', 'تنظیمات رزرو ذخیره شد.');
    }
}
