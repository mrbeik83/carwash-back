<?php

namespace App\Services;

use App\Models\BookingSlot;
use App\Models\CarWash;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SlotGenerationService
{
    public function generate(
        CarWash $carWash,
        CarbonImmutable $from,
        CarbonImmutable $to,
    ): int {
        $timezone = $carWash->timezone;
        $fromLocal = $from->setTimezone($timezone)->startOfDay();
        $toLocal = $to->setTimezone($timezone)->endOfDay();
        $created = 0;

        $rules = $carWash->capacityRules()
            ->where('is_active', true)
            ->get()
            ->groupBy('weekday');

        $exceptions = $carWash->scheduleExceptions()
            ->whereBetween('exception_date', [
                $fromLocal->toDateString(),
                $toLocal->toDateString(),
            ])
            ->get()
            ->groupBy(
                fn ($exception) => $exception->exception_date->toDateString()
            );

        DB::transaction(function () use (
            $carWash,
            $fromLocal,
            $toLocal,
            $rules,
            $exceptions,
            $timezone,
            &$created,
        ): void {
            foreach (CarbonPeriod::create(
                $fromLocal,
                '1 day',
                $toLocal,
            ) as $periodDay) {
                $date = CarbonImmutable::instance($periodDay)
                    ->setTimezone($timezone);

                /** @var Collection $dateExceptions */
                $dateExceptions = $exceptions->get(
                    $date->toDateString(),
                    collect(),
                );

                $fullDayClosed = $dateExceptions->contains(
                    fn ($exception): bool => $exception->is_closed
                        && $exception->start_time === null
                        && $exception->end_time === null,
                );

                $dayStartUtc = $date->startOfDay()->setTimezone('UTC');
                $dayEndUtc = $date->endOfDay()->setTimezone('UTC');

                if ($fullDayClosed) {
                    BookingSlot::query()
                        ->where('car_wash_id', $carWash->getKey())
                        ->whereBetween('starts_at', [$dayStartUtc, $dayEndUtc])
                        ->where('reserved_count', 0)
                        ->update(['status' => 'closed']);

                    continue;
                }

                foreach ($rules->get($date->dayOfWeek, collect()) as $rule) {
                    if (
                        $rule->valid_from
                        && $date->startOfDay()->isBefore(
                            $rule->valid_from->startOfDay()
                        )
                    ) {
                        continue;
                    }

                    if (
                        $rule->valid_until
                        && $date->startOfDay()->isAfter(
                            $rule->valid_until->endOfDay()
                        )
                    ) {
                        continue;
                    }

                    $cursor = CarbonImmutable::parse(
                        $date->toDateString().' '.$rule->start_time,
                        $timezone,
                    );

                    $end = CarbonImmutable::parse(
                        $date->toDateString().' '.$rule->end_time,
                        $timezone,
                    );

                    while (
                        $cursor->addMinutes(
                            $rule->slot_duration_minutes
                        )->lessThanOrEqualTo($end)
                    ) {
                        $slotEnd = $cursor->addMinutes(
                            $rule->slot_duration_minutes
                        );

                        $matchedException = $dateExceptions->first(
                            fn ($exception): bool => $this->coversSlot(
                                $exception,
                                $cursor,
                                $slotEnd,
                            ),
                        );

                        $isClosed = (bool) ($matchedException?->is_closed ?? false);
                        $capacity = (int) (
                            $matchedException?->capacity_override
                            ?? $rule->capacity
                        );

                        $slot = BookingSlot::query()->firstOrCreate(
                            [
                                'car_wash_id' => $carWash->getKey(),
                                'starts_at' => $cursor->setTimezone('UTC'),
                            ],
                            [
                                'ends_at' => $slotEnd->setTimezone('UTC'),
                                'capacity' => max(1, $capacity),
                                'reserved_count' => 0,
                                'status' => $isClosed ? 'closed' : 'open',
                                'source' => 'rule',
                            ],
                        );

                        if ($slot->wasRecentlyCreated) {
                            $created++;
                        } else {
                            $effectiveCapacity = max(
                                (int) $slot->reserved_count,
                                max(1, $capacity),
                            );

                            $status = $isClosed && $slot->reserved_count === 0
                                ? 'closed'
                                : (
                                    $slot->reserved_count >= $effectiveCapacity
                                        ? 'full'
                                        : 'open'
                                );

                            $slot->update([
                                'ends_at' => $slotEnd->setTimezone('UTC'),
                                'capacity' => $effectiveCapacity,
                                'status' => $status,
                            ]);
                        }

                        $cursor = $slotEnd;
                    }
                }
            }
        });

        return $created;
    }

    private function coversSlot(
        object $exception,
        CarbonImmutable $slotStart,
        CarbonImmutable $slotEnd,
    ): bool {
        if ($exception->start_time === null && $exception->end_time === null) {
            return true;
        }

        $exceptionStart = CarbonImmutable::parse(
            $slotStart->toDateString().' '.$exception->start_time,
            $slotStart->getTimezone(),
        );

        $exceptionEnd = CarbonImmutable::parse(
            $slotStart->toDateString().' '.$exception->end_time,
            $slotStart->getTimezone(),
        );

        return $slotStart->greaterThanOrEqualTo($exceptionStart)
            && $slotEnd->lessThanOrEqualTo($exceptionEnd);
    }
}
