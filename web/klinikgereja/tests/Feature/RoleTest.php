<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_seeder_creates_all_expected_roles(): void
    {
        $this->seed(RoleSeeder::class);

        $expectedRoles = [
            'Super Admin',
            'Admin',
            'Dokter',
            'Perawat',
            'Bendahara',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('roles', [
                'name' => $roleName,
            ]);
        }
    }

    public function test_user_has_role_and_helper_methods(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $doctorRole = Role::create(['name' => 'Dokter']);
        $nurseRole = Role::create(['name' => 'Perawat']);
        $treasurerRole = Role::create(['name' => 'Bendahara']);

        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $doctor = User::factory()->create(['role_id' => $doctorRole->id]);
        $nurse = User::factory()->create(['role_id' => $nurseRole->id]);
        $treasurer = User::factory()->create(['role_id' => $treasurerRole->id]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->isAdmin()); // Super Admin is considered admin
        $this->assertTrue($superAdmin->hasRole('Super Admin'));
        $this->assertTrue($superAdmin->hasRole(['Super Admin', 'Admin']));

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertTrue($admin->hasRole('Admin'));
        $this->assertFalse($admin->hasRole('Dokter'));

        $this->assertTrue($doctor->isDoctor());
        $this->assertFalse($doctor->isAdmin());

        $this->assertTrue($nurse->isNurse());
        $this->assertFalse($nurse->isAdmin());

        $this->assertTrue($treasurer->isTreasurer());
        $this->assertFalse($treasurer->isAdmin());
    }

    public function test_unauthorized_role_cannot_access_admin_routes(): void
    {
        $nurseRole = Role::create(['name' => 'Perawat']);
        $nurse = User::factory()->create(['role_id' => $nurseRole->id]);

        $response = $this->actingAs($nurse)->get('/patients');
        $response->assertStatus(403);

        $response = $this->actingAs($nurse)->get('/doctors');
        $response->assertStatus(403);

        $response = $this->actingAs($nurse)->get('/schedules');
        $response->assertStatus(403);
    }

    public function test_admin_and_super_admin_can_access_admin_routes(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->get('/patients');
        $response->assertStatus(200);

        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);

        $response = $this->actingAs($superAdmin)->get('/patients');
        $response->assertStatus(200);
    }
}
