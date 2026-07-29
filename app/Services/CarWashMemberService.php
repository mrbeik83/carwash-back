<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Enums\RoleName;
use App\Models\CarWash;
use App\Models\CarWashMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Illuminate\Validation\ValidationException;
use Throwable;

class CarWashMemberService
{
    /**
     * @throws Throwable
     */
    public function addOrUpdate(
        CarWash $carWash,
        User $user,
        RoleName $role,
        ?User $actor = null,
        ?string $jobTitle = null,
    ): void {
        if (! $role->isCarWashRole()) {
            throw new InvalidArgumentException(
                'Only car-wash roles can be assigned here.'
            );
        }

        if (
            $role === RoleName::CAR_WASH_OWNER
            && $actor !== null
            && ! $actor->is_super_admin
        ) {
            throw ValidationException::withMessages([
                'role' => 'فقط مدیر کل پلتفرم می‌تواند نقش مالک کارواش را اختصاص دهد.',
            ]);
        }

        DB::transaction(function () use (
            $carWash,
            $user,
            $role,
            $actor,
            $jobTitle,
        ): void {
            CarWashMembership::query()->updateOrCreate(
                [
                    'car_wash_id' => $carWash->getKey(),
                    'user_id' => $user->getKey(),
                ],
                [
                    'status' => MembershipStatus::ACTIVE->value,
                    'job_title' => $jobTitle,
                    'invited_by' => $actor?->getKey(),
                    'joined_at' => now(),
                    'suspended_at' => null,
                ],
            );

            $this->withinCarWash(
                $carWash,
                function () use ($user, $role): void {
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                    $user->syncRoles([$role->value]);
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                },
            );
        });
    }

    /**
     * @throws Throwable
     */
    public function suspend(CarWash $carWash, User $user): void
    {
        DB::transaction(function () use ($carWash, $user): void {
            DB::table('car_wash_user')
                ->where('car_wash_id', $carWash->getKey())
                ->where('user_id', $user->getKey())
                ->update([
                    'status' => MembershipStatus::SUSPENDED->value,
                    'suspended_at' => now(),
                    'updated_at' => now(),
                ]);

            $this->withinCarWash(
                $carWash,
                function () use ($user): void {
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                    $user->syncRoles([]);
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                },
            );
        });
    }

    /**
     * @throws Throwable
     */
    public function remove(CarWash $carWash, User $user): void
    {
        DB::transaction(function () use ($carWash, $user): void {
            $this->withinCarWash(
                $carWash,
                function () use ($user): void {
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                    $user->syncRoles([]);
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                },
            );

            DB::table('car_wash_user')
                ->where('car_wash_id', $carWash->getKey())
                ->where('user_id', $user->getKey())
                ->update([
                    'status' => MembershipStatus::REMOVED->value,
                    'updated_at' => now(),
                ]);
        });
    }

    private function withinCarWash(
        CarWash $carWash,
        callable $callback,
    ): mixed {
        $previousTeamId = getPermissionsTeamId();

        setPermissionsTeamId($carWash->getKey());

        try {
            return $callback();
        } finally {
            setPermissionsTeamId($previousTeamId);
        }
    }
}
