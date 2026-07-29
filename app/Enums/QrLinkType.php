<?php

namespace App\Enums;

enum QrLinkType: string
{
    case BOOKING = 'booking';
    case CAMPAIGN = 'campaign';
    case COUNTER = 'counter';
}
