<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار پرداخت',
            self::PROCESSING => 'در حال پردازش',
            self::PAID => 'پرداخت‌شده',
            self::FAILED => 'ناموفق',
            self::CANCELLED => 'لغوشده',
            self::PARTIALLY_REFUNDED => 'بازگشت بخشی از وجه',
            self::REFUNDED => 'بازگشت کامل وجه',
        };
    }
}
