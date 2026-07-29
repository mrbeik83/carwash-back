<?php

namespace Tests\Feature\Authorization;

use App\Enums\RoleName;
use App\Models\CarWash;
use App\Models\User;
use App\Services\CarWashMemberService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarWashTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_manager_cannot_open_another_car_wash_panel(): void
    {
        $user = User::factory()->create();
        $ownCarWash = CarWash::factory()->create(['status' => 'active']);
        $otherCarWash = CarWash::factory()->create(['status' => 'active']);

        app(CarWashMemberService::class)->addOrUpdate(
            $ownCarWash,
            $user,
            RoleName::CAR_WASH_MANAGER,
        );

        $this->actingAs($user)
            ->get(route('carwash.dashboard', $otherCarWash))
            ->assertForbidden();
    }

    public function test_super_admin_can_open_every_car_wash_panel(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $carWash = CarWash::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('carwash.dashboard', $carWash))
            ->assertOk();
    }
}
