<?php

namespace App\Enums;

enum BookingSource: string
{
    case WEB = 'web';
    case QR = 'qr';
    case PANEL = 'panel';
    case WALK_IN = 'walk_in';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::WEB => 'وب‌سایت',
            self::QR => 'کد QR',
            self::PANEL => 'پنل کارواش',
            self::WALK_IN => 'مراجعه حضوری',
            self::ADMIN => 'مدیر سیستم',
        };
    }
}
