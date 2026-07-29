<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\UpdateProfileRequest;
use App\Models\CarWash;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(CarWash $carWash): View
    {
        return view('carwash.profile.edit', compact('carWash'));
    }

    public function update(
        UpdateProfileRequest $request,
        CarWash $carWash,
        AuditService $audit,
    ): RedirectResponse {
        $old = $carWash->only(array_keys($request->validated()));
        $carWash->update($request->validated());

        $audit->record(
            action: 'carwash.profile_updated',
            subject: $carWash,
            oldValues: $old,
            newValues: $carWash->fresh()->only(array_keys($request->validated())),
            carWashId: $carWash->getKey(),
            actor: $request->user(),
            request: $request,
        );

        return back()->with('success', 'اطلاعات کارواش ذخیره شد.');
    }
}
