<?php

namespace Tests\Feature\Database;

use App\Models\User;
use App\Models\VehicleType;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_database_can_be_seeded_without_a_null_team_role_for_super_admin(): void
    {
        config()->set('carwash.super_admin', [
            'name' => 'مدیر کل تست',
            'mobile' => '989000000001',
            'email' => 'admin-test@example.com',
            'password' => 'Strong-Test-Password-123',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('mobile', '989000000001')->firstOrFail();

        $this->assertTrue($admin->is_super_admin);
        $this->assertSame(0, $admin->roles()->count());
        $this->assertTrue(Gate::forUser($admin)->allows('platform.access'));
        $this->assertSame(5, Role::query()->count());
        $this->assertGreaterThan(0, VehicleType::query()->count());
        $this->assertSame(0, DB::table('model_has_roles')->where('model_id', $admin->getKey())->count());
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('jobs'));
    }
}
