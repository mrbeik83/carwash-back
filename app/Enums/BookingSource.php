<?php

namespace App\Enums;

enum BookingSource: string
{
    case WEB = 'web';
    case QR = 'qr';
    case PANEL = 'panel';
    case WALK_IN = 'walk_in';
    case ADMIN = 'admin';
}
