<?php

namespace App\Enums;

enum RoleName: string
{
    case CAR_WASH_OWNER = 'carwash-owner';
    case CAR_WASH_MANAGER = 'carwash-manager';
    case CAR_WASH_RECEPTIONIST = 'carwash-receptionist';
    case CAR_WASH_OPERATOR = 'carwash-operator';
    case CAR_WASH_ACCOUNTANT = 'carwash-accountant';

    public function isCarWashRole(): bool
    {
        return true;
    }

    /**
     * @return array<int, string>
     */
    public static function carWashValues(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }
}
