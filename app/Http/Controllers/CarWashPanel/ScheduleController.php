<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\SaveWeeklyScheduleRequest;
use App\Http\Requests\CarWashPanel\StoreCapacityRuleRequest;
use App\Http\Requests\CarWashPanel\StoreScheduleExceptionRequest;
use App\Http\Requests\CarWashPanel\UpdateBookingSlotRequest;
use App\Models\BookingSlot;
use App\Models\CapacityRule;
use App\Models\CarWash;
use App\Models\ScheduleException;
use App\Services\SlotGenerationService;
use App\Support\PersianDate;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    private const WEEKDAY_ORDER = [6, 0, 1, 2, 3, 4, 5];

    private const WEEKDAY_LABELS = [
        0 => 'یکشنبه',
        1 => 'دوشنبه',
        2 => 'سه‌شنبه',
        3 => 'چهارشنبه',
        4 => 'پنجشنبه',
        5 => 'جمعه',
        6 => 'شنبه',
    ];

    public function index(Request $request, CarWash $carWash): View
    {
        $timezone = $carWash->timezone ?: 'Asia/Tehran';
        $weekStart = $this->weekStart(
            $request->string('week')->toString() ?: now($timezone)->toDateString(),
            $timezone,
        );
        $weekEnd = $weekStart->addDays(6)->endOfDay();

        $rules = $carWash->capacityRules()
            ->where('is_active', true)
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get();

        $weekSlots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [
                $weekStart->setTimezone('UTC'),
                $weekEnd->setTimezone('UTC'),
            ])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (BookingSlot $slot): string => $slot->starts_at->timezone($timezone)->toDateString());

        $weekDays = collect(self::WEEKDAY_ORDER)->map(function (int $offsetWeekday, int $index) use ($weekStart, $weekSlots, $timezone): array {
            $date = $weekStart->addDays($index);

            return [
                'weekday' => $offsetWeekday,
                'label' => self::WEEKDAY_LABELS[$offsetWeekday],
                'date' => $date,
                'date_key' => $date->toDateString(),
                'persian_date' => PersianDate::short($date, $timezone),
                'slots' => $weekSlots->get($date->toDateString(), collect()),
                'is_today' => $date->isToday(),
            ];
        });

        return view('carwash.schedule.index', [
            'carWash' => $carWash,
            'rules' => $rules,
            'rulesByDay' => $rules->groupBy('weekday'),
            'exceptions' => $carWash->scheduleExceptions()
                ->orderByDesc('exception_date')
                ->limit(50)
                ->get(),
            'weekdayOrder' => self::WEEKDAY_ORDER,
            'weekdayLabels' => self::WEEKDAY_LABELS,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weekDays' => $weekDays,
        ]);
    }

    public function saveWeekly(
        SaveWeeklyScheduleRequest $request,
        CarWash $carWash,
        SlotGenerationService $service,
    ): RedirectResponse {
        $days = $request->validated('days');

        DB::transaction(function () use ($days, $carWash, $request): void {
            foreach (range(0, 6) as $weekday) {
                $day = $days[$weekday] ?? ['enabled' => false];
                $rules = $carWash->capacityRules()->where('weekday', $weekday)->orderBy('id')->get();

                if (! (bool) ($day['enabled'] ?? false)) {
                    $carWash->capacityRules()->where('weekday', $weekday)->delete();
                    continue;
                }

                $slotCapacities = $this->normalizeSlotCapacities(
                    $day['start_time'],
                    $day['end_time'],
                    (int) $day['slot_duration_minutes'],
                    (int) $day['capacity'],
                    (array) ($day['slot_capacities'] ?? []),
                );

                $payload = [
                    'start_time' => $day['start_time'],
                    'end_time' => $day['end_time'],
                    'slot_duration_minutes' => (int) $day['slot_duration_minutes'],
                    'capacity' => (int) $day['capacity'],
                    'slot_capacities' => $slotCapacities,
                    'valid_from' => null,
                    'valid_until' => null,
                    'is_active' => true,
                    'created_by' => $request->user()->getKey(),
                ];

                $rule = $rules->first();
                if ($rule) {
                    $rule->update($payload);
                    $rules->skip(1)->each->delete();
                } else {
                    $carWash->capacityRules()->create(['weekday' => $weekday, ...$payload]);
                }
            }
        });

        $count = $service->generate(
            $carWash,
            CarbonImmutable::now('UTC'),
            CarbonImmutable::now('UTC')->addDays(
                $carWash->setting?->maximum_booking_days_ahead ?? 30
            ),
        );

        return back()->with('success', "برنامه هفتگی ذخیره شد و {$count} اسلات جدید ساخته شد.");
    }

    public function storeRule(
        StoreCapacityRuleRequest $request,
        CarWash $carWash,
    ): RedirectResponse {
        $carWash->capacityRules()->create([
            ...$request->validated(),
            'slot_capacities' => null,
            'created_by' => $request->user()->getKey(),
        ]);

        return back()->with('success', 'قانون ظرفیت اضافه شد.');
    }

    public function destroyRule(
        CarWash $carWash,
        CapacityRule $capacityRule,
    ): RedirectResponse {
        abort_unless($capacityRule->car_wash_id === $carWash->getKey(), 404);
        $capacityRule->delete();

        return back()->with('success', 'قانون ظرفیت حذف شد.');
    }

    public function updateSlot(
        UpdateBookingSlotRequest $request,
        CarWash $carWash,
        BookingSlot $bookingSlot,
    ): RedirectResponse {
        abort_unless($bookingSlot->car_wash_id === $carWash->getKey(), 404);

        $capacity = (int) $request->validated('capacity');
        $status = $request->validated('status');

        if ($capacity < $bookingSlot->reserved_count) {
            throw ValidationException::withMessages([
                'capacity' => 'ظرفیت نمی‌تواند کمتر از تعداد رزروهای ثبت‌شده باشد.',
            ]);
        }

        if ($status === 'closed' && $bookingSlot->reserved_count > 0) {
            throw ValidationException::withMessages([
                'status' => 'اسلات دارای رزرو را نمی‌توان بست. ابتدا رزروهای آن را جابه‌جا یا لغو کنید.',
            ]);
        }

        $bookingSlot->update([
            'capacity' => $capacity,
            'status' => $status === 'closed'
                ? 'closed'
                : ($bookingSlot->reserved_count >= $capacity ? 'full' : 'open'),
            'source' => 'manual',
        ]);

        return back()->with('success', 'ظرفیت اسلات به‌روزرسانی شد.');
    }

    public function storeException(
        StoreScheduleExceptionRequest $request,
        CarWash $carWash,
        SlotGenerationService $service,
    ): RedirectResponse {
        $carWash->scheduleExceptions()->create([
            ...$request->validated(),
            'start_time' => $request->validated('start_time'),
            'end_time' => $request->validated('end_time'),
            'capacity_override' => $request->boolean('is_closed')
                ? null
                : $request->validated('capacity_override'),
            'created_by' => $request->user()->getKey(),
        ]);

        $date = CarbonImmutable::parse($request->validated('exception_date'), $carWash->timezone);
        $service->generate($carWash, $date->startOfDay()->setTimezone('UTC'), $date->endOfDay()->setTimezone('UTC'));

        return back()->with('success', 'استثنای تقویم ثبت و اسلات‌های آن روز به‌روزرسانی شد.');
    }

    public function destroyException(
        CarWash $carWash,
        ScheduleException $scheduleException,
        SlotGenerationService $service,
    ): RedirectResponse {
        abort_unless($scheduleException->car_wash_id === $carWash->getKey(), 404);
        $date = CarbonImmutable::parse($scheduleException->exception_date, $carWash->timezone);
        $scheduleException->delete();
        $service->generate($carWash, $date->startOfDay()->setTimezone('UTC'), $date->endOfDay()->setTimezone('UTC'));

        return back()->with('success', 'استثنا حذف و برنامه آن روز بازسازی شد.');
    }

    public function regenerate(CarWash $carWash, SlotGenerationService $service): RedirectResponse
    {
        $count = $service->generate(
            $carWash,
            CarbonImmutable::now('UTC'),
            CarbonImmutable::now('UTC')->addDays(
                $carWash->setting?->maximum_booking_days_ahead ?? 30
            ),
        );

        return back()->with('success', "{$count} اسلات جدید ساخته شد و اسلات‌های قبلی همگام شدند.");
    }

    private function weekStart(string $date, string $timezone): CarbonImmutable
    {
        $cursor = CarbonImmutable::parse($date, $timezone)->startOfDay();

        while ($cursor->dayOfWeek !== CarbonImmutable::SATURDAY) {
            $cursor = $cursor->subDay();
        }

        return $cursor;
    }

    /** @return array<string,int> */
    private function normalizeSlotCapacities(
        string $startTime,
        string $endTime,
        int $duration,
        int $defaultCapacity,
        array $submitted,
    ): array {
        $result = [];
        $cursor = CarbonImmutable::createFromFormat('H:i', $startTime, 'UTC');
        $end = CarbonImmutable::createFromFormat('H:i', $endTime, 'UTC');

        while ($cursor->addMinutes($duration)->lessThanOrEqualTo($end)) {
            $key = $cursor->format('H:i');
            $result[$key] = max(1, min(100, (int) ($submitted[$key] ?? $defaultCapacity)));
            $cursor = $cursor->addMinutes($duration);
        }

        return $result;
    }
}
