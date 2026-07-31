<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SystemSetting::query()
            ->get()
            ->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:150'],
            'support_mobile' => ['nullable', 'string', 'max:20'],
            'support_email' => ['nullable', 'email', 'max:150'],
            'default_currency' => ['required', 'string', 'size:3'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => ['value' => $value],
                    'is_public' => in_array($key, [
                        'platform_name',
                        'support_mobile',
                        'support_email',
                    ], true),
                ],
            );
        }

        return back()->with('success', 'تنظیمات سامانه ذخیره شد.');
    }
}
