<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(null);

        foreach (PermissionName::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $allCarWash = $this->startingWith('carwash.');

        $roles = [
            RoleName::CAR_WASH_OWNER->value => $allCarWash,

            RoleName::CAR_WASH_MANAGER->value => $this->except($allCarWash, [
                'carwash.members.remove',
                'carwash.payments.refund',
                'carwash.audit.view',
            ]),

            RoleName::CAR_WASH_RECEPTIONIST->value => [
                'carwash.panel.access',
                'carwash.dashboard.view',
                'carwash.profile.view',
                'carwash.services.view',
                'carwash.schedule.view',
                'carwash.bookings.view',
                'carwash.bookings.create',
                'carwash.bookings.update',
                'carwash.bookings.confirm',
                'carwash.bookings.cancel',
                'carwash.bookings.check-in',
                'carwash.customers.view',
                'carwash.payments.view',
                'carwash.payments.create',
            ],

            RoleName::CAR_WASH_OPERATOR->value => [
                'carwash.panel.access',
                'carwash.dashboard.view',
                'carwash.profile.view',
                'carwash.services.view',
                'carwash.schedule.view',
                'carwash.bookings.view',
                'carwash.bookings.check-in',
                'carwash.bookings.start',
                'carwash.bookings.complete',
                'carwash.bookings.no-show',
            ],

            RoleName::CAR_WASH_ACCOUNTANT->value => [
                'carwash.panel.access',
                'carwash.dashboard.view',
                'carwash.profile.view',
                'carwash.bookings.view',
                'carwash.payments.view',
                'carwash.payments.create',
                'carwash.reports.view',
                'carwash.finance.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            Role::findOrCreate($roleName, 'web')
                ->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function startingWith(string $prefix): array
    {
        return collect(PermissionName::cases())
            ->map->value
            ->filter(
                static fn (string $permission): bool => str_starts_with(
                    $permission,
                    $prefix,
                )
            )
            ->values()
            ->all();
    }

    private function except(array $permissions, array $excluded): array
    {
        return array_values(array_diff($permissions, $excluded));
    }
}
