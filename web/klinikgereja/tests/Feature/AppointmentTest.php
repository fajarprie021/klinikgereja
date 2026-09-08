<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_number_increments_per_doctor_and_date(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $doctorRole = Role::create(['name' => 'Dokter']);
        $doctorUser = User::factory()->create(['role_id' => $doctorRole->id]);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'Test Doctor',
            'specialization' => 'General Practice',
        ]);
        $patient = Patient::create([
            'patient_number' => 'P-001',
            'name' => 'Test Patient',
            'patient_type' => 'UMUM',
        ]);

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'date' => now()->toDateString(),
        ];

        $this->actingAs($admin)->post(route('appointments.store'), $payload)->assertRedirect();
        $this->actingAs($admin)->post(route('appointments.store'), $payload)->assertRedirect();

        $this->assertSame([1, 2], Appointment::orderBy('queue_number')->pluck('queue_number')->all());
    }
}
