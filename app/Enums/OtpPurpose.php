<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case LOGIN = 'login';
    case REGISTER = 'register';
    case VERIFY_MOBILE = 'verify_mobile';
}
