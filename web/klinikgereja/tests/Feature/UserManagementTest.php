<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_manage_users(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $this->actingAs($superAdmin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/users')->assertForbidden();
    }

    public function test_super_admin_can_create_update_and_delete_a_user(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $doctorRole = Role::create(['name' => 'Dokter']);
        $superAdmin = User::factory()->create(['role_id' => $superAdminRole->id]);

        $response = $this->actingAs($superAdmin)->post('/users', [
            'name' => 'Dokter Baru',
            'username' => 'dokterbaru',
            'email' => 'dokterbaru@example.test',
            'password' => 'DokterBaru123!',
            'role_id' => $doctorRole->id,
            'status' => 'ACTIVE',
        ]);

        $response->assertRedirect('/users');
        $managedUser = User::where('username', 'dokterbaru')->firstOrFail();
        $this->assertTrue(password_verify('DokterBaru123!', $managedUser->password));

        $response = $this->actingAs($superAdmin)->put("/users/{$managedUser->id}", [
            'name' => 'Dokter Diperbarui',
            'username' => 'dokterupdate',
            'email' => 'dokterupdate@example.test',
            'role_id' => $doctorRole->id,
            'status' => 'INACTIVE',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'username' => 'dokterupdate',
            'status' => 'INACTIVE',
        ]);

        $this->actingAs($superAdmin)->delete("/users/{$managedUser->id}")->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        $role = Role::create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($superAdmin)->delete("/users/{$superAdmin->id}")->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }
}
