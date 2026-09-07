<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Dokter',
            'Perawat',
            'Bendahara',
        ];

        foreach ($roles as $roleName) {
            \App\Models\Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
