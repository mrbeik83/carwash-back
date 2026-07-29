<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'online';
    case POS = 'pos';
    case CASH = 'cash';
    case WALLET = 'wallet';
}
