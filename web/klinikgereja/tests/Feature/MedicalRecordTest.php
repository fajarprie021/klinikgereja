<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $doctorUser;
    private User $nurseUser;
    private Doctor $doctor;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin']);
        $doctorRole = Role::create(['name' => 'Dokter']);
        $nurseRole = Role::create(['name' => 'Perawat']);
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Bendahara']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->doctorUser = User::factory()->create(['role_id' => $doctorRole->id]);
        $this->nurseUser = User::factory()->create(['role_id' => $nurseRole->id]);

        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'name' => 'dr. Andreas',
            'specialization' => 'Umum',
        ]);

        $this->patient = Patient::create([
            'patient_number' => 'RM-001',
            'name' => 'Budi Santoso',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_date' => '1990-01-01',
            'phone' => '08123456789',
            'address' => 'Jakarta',
            'patient_type' => 'JEMAAT',
            'church_member_number' => 'GKJ-001',
        ]);
    }

    public function test_authorized_users_can_view_medical_records_list(): void
    {
        $response = $this->actingAs($this->doctorUser)->get('/medical-records');
        $response->assertStatus(200);

        $response = $this->actingAs($this->nurseUser)->get('/medical-records');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/medical-records');
        $response->assertStatus(200);
    }

    public function test_can_create_medical_record_with_vital_signs_and_complete_appointment(): void
    {
        $appointment = Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'date' => now()->toDateString(),
            'queue_number' => 1,
            'status' => 'CHECKED',
        ]);

        $payload = [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_id' => $appointment->id,
            'complaint' => 'Demam dan batuk sejak 3 hari',
            'diagnosis' => 'ISPA',
            'treatment' => 'Paracetamol 500mg 3x1, Ambroxol 30mg 3x1',
            'notes' => 'Banyak minum air hangat dan istirahat',
            'blood_pressure' => '120/80',
            'temperature' => 38.2,
            'weight' => 68.5,
            'height' => 172.0,
            'heart_rate' => 84,
            'respiratory_rate' => 20,
            'vital_notes' => 'Pasien tampak lemas',
        ];

        $response = $this->actingAs($this->doctorUser)->post('/medical-records', $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_id' => $appointment->id,
            'complaint' => 'Demam dan batuk sejak 3 hari',
            'diagnosis' => 'ISPA',
        ]);

        $this->assertDatabaseHas('vital_signs', [
            'blood_pressure' => '120/80',
            'temperature' => '38.2',
            'weight' => '68.50',
            'height' => '172.00',
        ]);

        // Verify appointment status transitioned to DONE
        $this->assertEquals('DONE', $appointment->fresh()->status);
    }

    public function test_can_update_medical_record(): void
    {
        $record = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Sakit kepala',
            'diagnosis' => 'Cephalgia',
            'treatment' => 'Paracetamol',
        ]);

        $updatePayload = [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Sakit kepala sebelah (migrain)',
            'diagnosis' => 'Migraine',
            'treatment' => 'Sumatriptan 50mg',
            'notes' => 'Hindari stres',
            'blood_pressure' => '130/85',
            'temperature' => 36.8,
            'weight' => 70.0,
            'height' => 172.0,
        ];

        $response = $this->actingAs($this->doctorUser)->put("/medical-records/{$record->id}", $updatePayload);
        $response->assertRedirect("/medical-records/{$record->id}");

        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'diagnosis' => 'Migraine',
        ]);

        $this->assertDatabaseHas('vital_signs', [
            'medical_record_id' => $record->id,
            'blood_pressure' => '130/85',
        ]);
    }

    public function test_vital_sign_can_be_stored_separately(): void
    {
        $record = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Pemeriksaan rutin',
        ]);

        $vitalPayload = [
            'medical_record_id' => $record->id,
            'blood_pressure' => '110/70',
            'temperature' => 36.4,
            'weight' => 60.0,
            'height' => 165.0,
            'heart_rate' => 76,
            'respiratory_rate' => 18,
            'notes' => 'Kondisi stabil',
        ];

        $response = $this->actingAs($this->nurseUser)->post('/vital-signs', $vitalPayload);
        $response->assertRedirect("/medical-records/{$record->id}");

        $this->assertDatabaseHas('vital_signs', [
            'medical_record_id' => $record->id,
            'blood_pressure' => '110/70',
            'heart_rate' => 76,
        ]);
    }
}
