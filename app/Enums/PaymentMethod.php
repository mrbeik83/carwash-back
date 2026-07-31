<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'online';
    case POS = 'pos';
    case CASH = 'cash';
    case WALLET = 'wallet';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'پرداخت آنلاین',
            self::POS => 'کارت‌خوان',
            self::CASH => 'نقدی',
            self::WALLET => 'کیف پول',
        };
    }
}
