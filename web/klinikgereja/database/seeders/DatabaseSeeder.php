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

        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();
        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $doctorRole = Role::where('name', 'Dokter')->firstOrFail();
        $nurseRole = Role::where('name', 'Perawat')->firstOrFail();
        $treasurerRole = Role::where('name', 'Bendahara')->firstOrFail();

        // 1. Super Admin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'password' => 'SuperAdmin123!',
                'role_id' => $superAdminRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        // 2. Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Klinik',
                'password' => 'Admin123!',
                'role_id' => $adminRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        // 3. Dokter
        $doctorUser = User::updateOrCreate(
            ['username' => 'dokter'],
            [
                'name' => 'dr. Budi Santoso, Sp.PD',
                'password' => 'Dokter123!',
                'role_id' => $doctorRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        \App\Models\Doctor::updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'name' => 'dr. Budi Santoso, Sp.PD',
                'specialization' => 'Spesialis Penyakit Dalam',
            ]
        );

        // 4. Perawat
        User::updateOrCreate(
            ['username' => 'perawat'],
            [
                'name' => 'Sr. Maria Magdalena',
                'password' => 'Perawat123!',
                'role_id' => $nurseRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        // 5. Bendahara
        User::updateOrCreate(
            ['username' => 'bendahara'],
            [
                'name' => 'Bendahara Pelayanan Gereja',
                'password' => 'Bendahara123!',
                'role_id' => $treasurerRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        // Sample Pasien Awal
        \App\Models\Patient::firstOrCreate(
            ['patient_number' => 'RM-202609-0001'],
            [
                'name' => 'Yohanes Prasetyo',
                'nik' => '3201015504850001',
                'gender' => 'L',
                'birth_date' => '1985-04-15',
                'phone' => '081234567890',
                'address' => 'Jl. Kasih No. 10',
                'patient_type' => 'JEMAAT',
                'church_member_number' => 'GKJ-001',
            ]
        );

        \App\Models\Patient::firstOrCreate(
            ['patient_number' => 'RM-202609-0002'],
            [
                'name' => 'Siti Rahmawati',
                'nik' => '3201016008920002',
                'gender' => 'P',
                'birth_date' => '1992-08-20',
                'phone' => '081398765432',
                'address' => 'Jl. Mawar No. 4',
                'patient_type' => 'UMUM',
            ]
        );
    }
}
