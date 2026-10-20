<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Subsidy;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VitalSign;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_related_synthetic_records_and_role_accounts(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $admin = User::factory()->create([
            'username' => 'admin',
            'role_id' => $adminRole->id,
        ]);

        $this->seed(DemoDataSeeder::class);

        $this->assertSame(5, Patient::count());
        $this->assertSame(3, Doctor::count());
        $this->assertSame(6, Schedule::count());
        $this->assertSame(0, Schedule::whereNotIn('day', ['Monday', 'Wednesday'])->count());
        $this->assertSame(5, Appointment::count());
        $this->assertSame(3, MedicalRecord::count());
        $this->assertSame(3, VitalSign::count());
        $this->assertSame(1, Subsidy::count());
        $this->assertSame(1, Transaction::count());
        $this->assertSame('DEMO-INV-0001', Transaction::firstOrFail()->invoice_number);
        $this->assertSame(7, User::count());
        $this->assertSame($admin->id, Subsidy::first()->approved_by);
        $this->assertTrue(Hash::check('Demo123!', User::where('username', 'demo-nurse')->value('password')));
        $this->assertSame('Dokter', User::where('username', 'demo-doctor-1')->firstOrFail()->role->name);
    }

    public function test_demo_seeder_can_be_run_repeatedly_without_duplicating_records(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(5, Patient::count());
        $this->assertSame(3, Doctor::count());
        $this->assertSame(6, Schedule::count());
        $this->assertSame(0, Schedule::whereNotIn('day', ['Monday', 'Wednesday'])->count());
        $this->assertSame(5, Appointment::count());
        $this->assertSame(3, MedicalRecord::count());
        $this->assertSame(3, VitalSign::count());
        $this->assertSame(1, Subsidy::count());
        $this->assertSame(1, Transaction::count());
        $this->assertSame(6, User::count());
    }
}
