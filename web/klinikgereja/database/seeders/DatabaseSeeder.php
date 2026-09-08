<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed role base first
        $this->call([
            RoleSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Klinik',
                'email' => null,
                'password' => 'Admin123!',
                'role_id' => $adminRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ],
        );
    }
}
