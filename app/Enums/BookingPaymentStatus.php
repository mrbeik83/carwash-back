<?php

namespace App\Enums;

enum BookingPaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'پرداخت‌نشده',
            self::PARTIAL => 'پرداخت ناقص',
            self::PAID => 'پرداخت‌شده',
            self::REFUNDED => 'بازگشت کامل وجه',
            self::PARTIALLY_REFUNDED => 'بازگشت بخشی از وجه',
        };
    }
}
