<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case BLOCKED = 'blocked';
}
