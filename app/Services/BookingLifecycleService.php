<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookingLifecycleService
{
    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled', 'rejected'],
        'confirmed' => ['checked_in', 'cancelled', 'no_show'],
        'checked_in' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
        'no_show' => [],
        'rejected' => [],
    ];

    public function __construct(private readonly AuditService $audit)
    {
    }

    /** @throws Throwable */
    public function transition(
        Booking $booking,
        BookingStatus $to,
        ?User $actor = null,
        ?string $note = null,
    ): Booking {
        return DB::transaction(function () use ($booking, $to, $actor, $note): Booking {
            $locked = Booking::query()->whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $from = $locked->status->value;

            if (! in_array($to->value, self::TRANSITIONS[$from] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => "تغییر وضعیت از {$from} به {$to->value} مجاز نیست.",
                ]);
            }

            $timestamps = match ($to) {
                BookingStatus::CONFIRMED => ['confirmed_at' => now()],
                BookingStatus::CHECKED_IN => ['checked_in_at' => now()],
                BookingStatus::IN_PROGRESS => ['started_at' => now()],
                BookingStatus::COMPLETED => ['completed_at' => now()],
                BookingStatus::CANCELLED => ['cancelled_at' => now(), 'cancelled_by' => $actor?->getKey()],
                default => [],
            };

            $locked->update([
                'status' => $to,
                ...$timestamps,
                ...($to === BookingStatus::CANCELLED ? ['cancellation_reason' => $note] : []),
            ]);

            $locked->statusHistory()->create([
                'from_status' => $from,
                'to_status' => $to->value,
                'changed_by' => $actor?->getKey(),
                'note' => $note,
                'created_at' => now(),
            ]);

            if (in_array($to, [BookingStatus::CANCELLED, BookingStatus::REJECTED], true)) {
                $slot = $locked->slot()->lockForUpdate()->first();
                if ($slot && $slot->reserved_count > 0) {
                    $newReservedCount = max(0, (int) $slot->reserved_count - 1);

                    $slot->update([
                        'reserved_count' => $newReservedCount,
                        'status' => $slot->status === 'closed'
                            ? 'closed'
                            : ($newReservedCount >= $slot->capacity ? 'full' : 'open'),
                    ]);
                }
            }

            $this->audit->record(
                action: 'booking.status_changed',
                subject: $locked,
                oldValues: ['status' => $from],
                newValues: ['status' => $to->value],
                carWashId: $locked->car_wash_id,
                actor: $actor,
            );

            return $locked->fresh(['items', 'slot', 'payments']);
        });
    }
}
