<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case INVITED = 'invited';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case REMOVED = 'removed';
}
