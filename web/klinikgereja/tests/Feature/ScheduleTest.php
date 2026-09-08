<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_overlapping_schedule_for_same_doctor_and_day(): void
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

        Schedule::create([
            'doctor_id' => $doctor->id,
            'day' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        $response = $this->actingAs($admin)->post(route('schedules.store'), [
            'doctor_id' => $doctor->id,
            'day' => 'Monday',
            'start_time' => '11:00',
            'end_time' => '13:00',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertSame(1, Schedule::count());
    }
}
