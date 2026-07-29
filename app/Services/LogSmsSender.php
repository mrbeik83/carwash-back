<?php

namespace App\Services;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Log;

class LogSmsSender implements SmsSender
{
    public function send(string $mobile, string $message): void
    {
        // فقط برای محیط توسعه؛ در production با درگاه پیامک واقعی جایگزین شود.
        Log::info('SMS', compact('mobile', 'message'));
    }
}
