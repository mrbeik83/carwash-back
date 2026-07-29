<?php

namespace App\Http\Middleware;

use App\Enums\CarWashStatus;
use App\Support\CurrentCarWash;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveCarWashMember
{
    public function __construct(
        private readonly CurrentCarWash $currentCarWash,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if ($user->is_super_admin) {
            return $next($request);
        }

        abort_unless(
            $this->currentCarWash->get()->status === CarWashStatus::ACTIVE,
            403,
            'این کارواش در حال حاضر فعال نیست.',
        );

        $isActiveMember = $user->activeCarWashes()
            ->whereKey($this->currentCarWash->id())
            ->exists();

        abort_unless($isActiveMember, 403, 'عضویت فعال در این کارواش ندارید.');

        return $next($request);
    }
}
