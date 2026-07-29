<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\StoreCapacityRuleRequest;
use App\Http\Requests\CarWashPanel\StoreScheduleExceptionRequest;
use App\Models\CapacityRule;
use App\Models\CarWash;
use App\Models\ScheduleException;
use App\Services\SlotGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(CarWash $carWash): View
    {
        return view('carwash.schedule.index', [
            'carWash' => $carWash,
            'rules' => $carWash->capacityRules()
                ->orderBy('weekday')
                ->orderBy('start_time')
                ->get(),
            'exceptions' => $carWash->scheduleExceptions()
                ->orderByDesc('exception_date')
                ->limit(50)
                ->get(),
        ]);
    }

    public function storeRule(
        StoreCapacityRuleRequest $request,
        CarWash $carWash,
    ): RedirectResponse {
        $carWash->capacityRules()->create([
            ...$request->validated(),
            'created_by' => $request->user()->getKey(),
        ]);

        return back()->with('success', 'قانون ظرفیت اضافه شد.');
    }

    public function destroyRule(
        CarWash $carWash,
        CapacityRule $capacityRule,
    ): RedirectResponse {
        abort_unless(
            $capacityRule->car_wash_id === $carWash->getKey(),
            404,
        );

        $capacityRule->delete();

        return back()->with('success', 'قانون ظرفیت حذف شد.');
    }

    public function storeException(
        StoreScheduleExceptionRequest $request,
        CarWash $carWash,
    ): RedirectResponse {
        $carWash->scheduleExceptions()->create([
            ...$request->validated(),
            // If times are omitted, a closed exception applies to the whole day.
            // If times are supplied, it represents a partial-day closure.
            'start_time' => $request->validated('start_time'),
            'end_time' => $request->validated('end_time'),
            'capacity_override' => $request->boolean('is_closed')
                ? null
                : $request->validated('capacity_override'),
            'created_by' => $request->user()->getKey(),
        ]);

        return back()->with('success', 'استثنای تقویم ثبت شد.');
    }

    public function destroyException(
        CarWash $carWash,
        ScheduleException $scheduleException,
    ): RedirectResponse {
        abort_unless(
            $scheduleException->car_wash_id === $carWash->getKey(),
            404,
        );

        $scheduleException->delete();

        return back()->with('success', 'استثنا حذف شد.');
    }

    public function regenerate(
        CarWash $carWash,
        SlotGenerationService $service,
    ): RedirectResponse {
        $count = $service->generate(
            $carWash,
            CarbonImmutable::now('UTC'),
            CarbonImmutable::now('UTC')->addDays(
                $carWash->setting?->maximum_booking_days_ahead ?? 30
            ),
        );

        return back()->with('success', "{$count} بازه جدید ساخته شد.");
    }
}
