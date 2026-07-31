<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CHECKED_IN = 'checked_in';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار تأیید',
            self::CONFIRMED => 'تأییدشده',
            self::CHECKED_IN => 'مراجعه کرده',
            self::IN_PROGRESS => 'در حال شست‌وشو',
            self::COMPLETED => 'تکمیل‌شده',
            self::CANCELLED => 'لغوشده',
            self::NO_SHOW => 'عدم مراجعه',
            self::REJECTED => 'ردشده',
        };
    }
}
