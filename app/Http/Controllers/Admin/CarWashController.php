<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CarWashes\CreateCarWashAction;
use App\Enums\CarWashStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarWashRequest;
use App\Models\CarWash;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarWashController extends Controller
{
    public function index(Request $request): View
    {
        $carWashes = CarWash::query()
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->toString(),
                ),
            )
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';

                $query->where(
                    fn ($inner) => $inner
                        ->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term)
                );
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.car-washes.index', compact('carWashes'));
    }

    public function create(): View
    {
        return view('admin.car-washes.create');
    }

    public function store(
        StoreCarWashRequest $request,
        CreateCarWashAction $action,
    ): RedirectResponse {
        $carWash = $action->execute(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('admin.car-washes.show', $carWash)
            ->with('success', 'کارواش و مالک آن ایجاد شدند.');
    }

    public function show(CarWash $carWash): View
    {
        return view('admin.car-washes.show', [
            'carWash' => $carWash->load(['members', 'setting']),
        ]);
    }

    public function edit(CarWash $carWash): View
    {
        return view('admin.car-washes.edit', compact('carWash'));
    }

    public function update(
        StoreCarWashRequest $request,
        CarWash $carWash,
        AuditService $audit,
    ): RedirectResponse {
        $data = $request->safe()->except(['owner_name', 'owner_mobile', 'status']);
        $old = $carWash->only(array_keys($data));

        $carWash->update($data);

        $audit->record(
            action: 'platform.carwash_updated',
            subject: $carWash,
            oldValues: $old,
            newValues: $carWash->fresh()->only(array_keys($data)),
            carWashId: $carWash->getKey(),
            actor: $request->user(),
            request: $request,
        );

        return redirect()
            ->route('admin.car-washes.show', $carWash)
            ->with('success', 'اطلاعات کارواش به‌روزرسانی شد.');
    }

    public function changeStatus(
        Request $request,
        CarWash $carWash,
        AuditService $audit,
    ): RedirectResponse {
        $data = $request->validate([
            'status' => ['required', Rule::enum(CarWashStatus::class)],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = CarWashStatus::from($data['status']);
        $old = $carWash->status->value;

        $carWash->update([
            'status' => $status,
            'approved_by' => $status === CarWashStatus::ACTIVE
                ? $request->user()->getKey()
                : $carWash->approved_by,
            'approved_at' => $status === CarWashStatus::ACTIVE
                ? now()
                : $carWash->approved_at,
            'suspended_at' => $status === CarWashStatus::SUSPENDED
                ? now()
                : null,
            'suspension_reason' => $status === CarWashStatus::SUSPENDED
                ? ($data['reason'] ?? null)
                : null,
        ]);

        $audit->record(
            action: 'platform.carwash_status_changed',
            subject: $carWash,
            oldValues: ['status' => $old],
            newValues: ['status' => $status->value],
            carWashId: $carWash->getKey(),
            actor: $request->user(),
            request: $request,
        );

        return back()->with('success', 'وضعیت کارواش تغییر کرد.');
    }
}
