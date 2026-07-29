<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\CarWash;
use App\Models\CarWashInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CarWashInvitationService
{
    public function __construct(
        private readonly CarWashMemberService $members,
    ) {
    }

    /**
     * @return array{invitation: CarWashInvitation, token: string}
     */
    public function create(
        CarWash $carWash,
        User $inviter,
        RoleName $role,
        ?string $mobile,
        ?string $email,
    ): array {
        if (! $role->isCarWashRole()) {
            throw ValidationException::withMessages([
                'role' => 'نقش دعوت باید مربوط به پنل کارواش باشد.',
            ]);
        }

        if ($role === RoleName::CAR_WASH_OWNER && ! $inviter->is_super_admin) {
            throw ValidationException::withMessages([
                'role' => 'فقط مدیر کل پلتفرم می‌تواند برای نقش مالک دعوت‌نامه بسازد.',
            ]);
        }

        if (! $mobile && ! $email) {
            throw ValidationException::withMessages([
                'mobile' => 'شماره موبایل یا ایمیل الزامی است.',
            ]);
        }

        $plainToken = Str::random(64);

        $invitation = CarWashInvitation::query()->create([
            'car_wash_id' => $carWash->getKey(),
            'mobile' => $mobile,
            'email' => $email ? mb_strtolower($email) : null,
            'role_name' => $role->value,
            'token_hash' => hash('sha256', $plainToken),
            'invited_by' => $inviter->getKey(),
            'expires_at' => now()->addDays(3),
        ]);

        return ['invitation' => $invitation, 'token' => $plainToken];
    }

    public function findUsable(string $plainToken, User $user): CarWashInvitation
    {
        $invitation = CarWashInvitation::query()
            ->with(['carWash', 'inviter'])
            ->where('token_hash', hash('sha256', $plainToken))
            ->firstOrFail();

        if (! $invitation->isUsable()) {
            throw ValidationException::withMessages([
                'token' => 'دعوت‌نامه منقضی، لغو یا قبلاً استفاده شده است.',
            ]);
        }

        $this->assertRecipient($invitation, $user);

        return $invitation;
    }

    /**
     * @throws Throwable
     */
    public function accept(string $plainToken, User $user): CarWashInvitation
    {
        return DB::transaction(function () use ($plainToken, $user): CarWashInvitation {
            $invitation = CarWashInvitation::query()
                ->where('token_hash', hash('sha256', $plainToken))
                ->lockForUpdate()
                ->firstOrFail();

            if (! $invitation->isUsable()) {
                throw ValidationException::withMessages([
                    'token' => 'دعوت‌نامه منقضی، لغو یا قبلاً استفاده شده است.',
                ]);
            }

            $this->assertRecipient($invitation, $user);

            $role = RoleName::from($invitation->role_name);

            $this->members->addOrUpdate(
                $invitation->carWash,
                $user,
                $role,
                $invitation->inviter,
            );

            $invitation->update([
                'accepted_by' => $user->getKey(),
                'accepted_at' => now(),
            ]);

            return $invitation->fresh(['carWash']);
        });
    }

    private function assertRecipient(CarWashInvitation $invitation, User $user): void
    {
        if ($invitation->mobile && $user->mobile !== $invitation->mobile) {
            throw ValidationException::withMessages([
                'mobile' => 'این دعوت‌نامه برای شماره موبایل دیگری صادر شده است.',
            ]);
        }

        if (
            $invitation->email
            && mb_strtolower((string) $user->email) !== mb_strtolower($invitation->email)
        ) {
            throw ValidationException::withMessages([
                'email' => 'این دعوت‌نامه برای ایمیل دیگری صادر شده است.',
            ]);
        }
    }
}
