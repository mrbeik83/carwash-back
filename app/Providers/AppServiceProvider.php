<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Models\User;
use App\Services\LogSmsSender;
use App\Support\CurrentCarWash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CurrentCarWash::class);
        $this->app->bind(SmsSender::class, LogSmsSender::class);
    }

    public function boot(): void
    {
        Gate::before(
            static fn (User $user, string $ability): ?bool => $user->is_super_admin
                ? true
                : null,
        );
    }
}
