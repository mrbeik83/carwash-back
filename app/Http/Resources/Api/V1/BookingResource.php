<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Support\PersianDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $timezone = $this->carWash?->timezone ?: 'Asia/Tehran';
        $status = $this->status instanceof BookingStatus
            ? $this->status
            : BookingStatus::tryFrom((string) $this->status);
        $source = $this->source instanceof BookingSource
            ? $this->source
            : BookingSource::tryFrom((string) $this->source);
        $paymentStatus = $this->payment_status instanceof PaymentStatus
            ? $this->payment_status
            : PaymentStatus::tryFrom((string) $this->payment_status);

        return [
            'id' => $this->public_id,
            'tracking_code' => $this->tracking_code,
            'status' => [
                'value' => $status?->value ?? (string) $this->status,
                'label' => $status?->label() ?? (string) $this->status,
            ],
            'payment_status' => [
                'value' => $paymentStatus?->value ?? (string) $this->payment_status,
                'label' => $paymentStatus?->label() ?? (string) $this->payment_status,
            ],
            'source' => [
                'value' => $source?->value ?? (string) $this->source,
                'label' => $source?->label() ?? (string) $this->source,
            ],
            'customer' => [
                'name' => $this->customer_name,
                'mobile' => $this->customer_mobile,
            ],
            'vehicle' => [
                'id' => $this->vehicle_id,
                'plate' => $this->vehicle_plate_snapshot,
                'type' => $this->vehicle_type_snapshot,
            ],
            'amounts' => [
                'subtotal' => (int) $this->subtotal_amount,
                'discount' => (int) $this->discount_amount,
                'payable' => (int) $this->payable_amount,
                'currency_code' => $this->currency_code,
            ],
            'customer_note' => $this->customer_note,
            'car_wash' => $this->whenLoaded('carWash', fn (): ?array => $this->carWash ? [
                'id' => $this->carWash->public_id,
                'name' => $this->carWash->name,
                'slug' => $this->carWash->slug,
                'timezone' => $timezone,
                'address' => $this->carWash->address,
                'phone' => $this->carWash->phone,
                'mobile' => $this->carWash->mobile,
            ] : null),
            'slot' => $this->whenLoaded('slot', function () use ($timezone): ?array {
                if (! $this->slot) {
                    return null;
                }

                $startsAt = $this->slot->starts_at->timezone($timezone);
                $endsAt = $this->slot->ends_at->timezone($timezone);

                return [
                    'id' => $this->slot->id,
                    'starts_at' => $this->slot->starts_at->toIso8601String(),
                    'ends_at' => $this->slot->ends_at->toIso8601String(),
                    'date' => $startsAt->toDateString(),
                    'persian_date' => PersianDate::date($startsAt, $timezone),
                    'persian_date_label' => PersianDate::human($startsAt, $timezone),
                    'weekday' => PersianDate::weekday($startsAt, $timezone),
                    'local_start_time' => $startsAt->format('H:i'),
                    'local_end_time' => $endsAt->format('H:i'),
                ];
            }),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item): array => [
                'service_id' => $item->service_id,
                'name' => $item->service_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (int) $item->unit_price,
                'duration_minutes' => (int) $item->duration_minutes,
                'discount_amount' => (int) $item->discount_amount,
                'total_amount' => (int) $item->total_amount,
            ])->values()),
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(function ($payment): array {
                $method = $payment->method instanceof PaymentMethod
                    ? $payment->method
                    : PaymentMethod::tryFrom((string) $payment->method);
                $status = $payment->status instanceof PaymentStatus
                    ? $payment->status
                    : PaymentStatus::tryFrom((string) $payment->status);

                return [
                    'id' => $payment->public_id,
                    'amount' => (int) $payment->amount,
                    'currency_code' => $payment->currency_code,
                    'method' => [
                        'value' => $method?->value ?? (string) $payment->method,
                        'label' => $method?->label() ?? (string) $payment->method,
                    ],
                    'status' => [
                        'value' => $status?->value ?? (string) $payment->status,
                        'label' => $status?->label() ?? (string) $payment->status,
                    ],
                    'reference_id' => $payment->reference_id,
                    'paid_at' => $payment->paid_at?->toIso8601String(),
                ];
            })->values()),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
