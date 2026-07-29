<?php

namespace App\Actions\CarWashes;

use App\Enums\CarWashStatus;
use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\CarWash;
use App\Models\User;
use App\Services\CarWashMemberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CreateCarWashAction
{
    public function __construct(
        private readonly CarWashMemberService $members,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function execute(array $data, User $actor): CarWash
    {
        return DB::transaction(function () use ($data, $actor): CarWash {
            $owner = User::query()->firstOrCreate(
                ['mobile' => $data['owner_mobile']],
                [
                    'full_name' => $data['owner_name'],
                    'status' => UserStatus::ACTIVE,
                    'mobile_verified_at' => now(),
                ],
            );

            $owner->forceFill([
                'full_name' => blank($owner->full_name)
                    ? $data['owner_name']
                    : $owner->full_name,
                'status' => UserStatus::ACTIVE,
                'mobile_verified_at' => $owner->mobile_verified_at ?? now(),
            ])->save();

            $carWash = CarWash::query()->create([
                'name' => $data['name'],
                'slug' => ($data['slug'] ?? null) ?: $this->uniqueSlug($data['name']),
                'code' => ($data['code'] ?? null) ?: $this->uniqueCode(),
                'status' => $data['status'] ?? CarWashStatus::PENDING,
                'phone' => $data['phone'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'email' => $data['email'] ?? null,
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'timezone' => $data['timezone'] ?? 'Asia/Tehran',
                'currency_code' => 'IRR',
                'approved_by' => ($data['status'] ?? null) === CarWashStatus::ACTIVE->value ? $actor->getKey() : null,
                'approved_at' => ($data['status'] ?? null) === CarWashStatus::ACTIVE->value ? now() : null,
                'created_by' => $actor->getKey(),
            ]);

            $carWash->setting()->create([]);

            $this->members->addOrUpdate(
                carWash: $carWash,
                user: $owner,
                role: RoleName::CAR_WASH_OWNER,
                actor: $actor,
                jobTitle: 'مالک کارواش',
            );

            return $carWash->load(['setting', 'members']);
        });
    }
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'car-wash';

        do {
            $slug = $base.'-'.Str::lower(Str::random(6));
        } while (CarWash::query()->where('slug', $slug)->exists());

        return $slug;
    }

    private function uniqueCode(): string
    {
        do {
            $code = 'CW-'.Str::upper(Str::random(8));
        } while (CarWash::query()->where('code', $code)->exists());

        return $code;
    }

}
