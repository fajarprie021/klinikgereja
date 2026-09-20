<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;
    private User $doctorUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        $doctorRole = Role::create(['name' => 'Dokter']);
        Role::create(['name' => 'Perawat']);
        Role::create(['name' => 'Bendahara']);
        Role::create(['name' => 'Pasien']);

        $this->doctorUser = User::factory()->create(['role_id' => $doctorRole->id]);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'name' => 'dr. Elisabeth',
            'specialization' => 'Spesialis Penyakit Dalam',
        ]);

        Schedule::create([
            'doctor_id' => $this->doctor->id,
            'day' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '12:00',
        ]);
    }

    public function test_patient_registration_via_mobile_api(): void
    {
        $payload = [
            'name' => 'Agus Hartono',
            'username' => 'agushartono',
            'password' => 'secret123',
            'nik' => '3201019901010001',
            'gender' => 'L',
            'birth_date' => '1995-03-20',
            'phone' => '081298765432',
            'address' => 'Jl. Damai No. 12',
            'patient_type' => 'JEMAAT',
            'church_member_number' => 'GKJ-777',
        ];

        $response = $this->postJson('/api/v1/mobile/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'data' => [
                    'user' => ['id', 'name', 'username'],
                    'patient' => ['id', 'patient_number', 'name', 'nik', 'patient_type'],
                ],
            ]);

        $this->assertDatabaseHas('users', ['username' => 'agushartono']);
        $this->assertDatabaseHas('patients', ['nik' => '3201019901010001', 'church_member_number' => 'GKJ-777']);
    }

    public function test_patient_login_and_me_and_logout(): void
    {
        $patientRole = Role::where('name', 'Pasien')->first();
        $user = User::factory()->create([
            'username' => 'pasien1',
            'password' => bcrypt('password123'),
            'role_id' => $patientRole->id,
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'patient_number' => 'RM-003',
            'name' => 'Pasien Satu',
            'nik' => '3201010101010099',
            'gender' => 'L',
            'birth_date' => '1992-02-02',
            'phone' => '081200000000',
            'address' => 'Jakarta Barat',
            'patient_type' => 'UMUM',
        ]);

        // Login
        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'username' => 'pasien1',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'data']);

        $token = $response->json('token');

        // Check me endpoint
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/mobile/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.user.username', 'pasien1')
            ->assertJsonPath('data.patient.patient_number', 'RM-003');

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/mobile/auth/logout');

        $logoutResponse->assertStatus(200);
    }

    public function test_patient_can_view_doctors_and_book_appointment(): void
    {
        // Public doctors endpoint
        $docResponse = $this->getJson('/api/v1/mobile/doctors');
        $docResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $patientRole = Role::where('name', 'Pasien')->first();
        $user = User::factory()->create(['role_id' => $patientRole->id]);
        $patient = Patient::create([
            'user_id' => $user->id,
            'patient_number' => 'RM-004',
            'name' => 'Pasien Booking',
            'nik' => '3201010101010088',
            'gender' => 'P',
            'birth_date' => '1994-04-04',
            'phone' => '081211111111',
            'address' => 'Jakarta Selatan',
            'patient_type' => 'JEMAAT',
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $bookingDate = now()->addDay()->toDateString();

        $bookResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/mobile/appointments/book', [
                'doctor_id' => $this->doctor->id,
                'date' => $bookingDate,
            ]);

        $bookResponse->assertStatus(201)
            ->assertJsonPath('data.queue_number', 1)
            ->assertJsonPath('data.status', 'WAITING');

        // Check duplicate booking error
        $dupResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/mobile/appointments/book', [
                'doctor_id' => $this->doctor->id,
                'date' => $bookingDate,
            ]);

        $dupResponse->assertStatus(422);
    }

    public function test_patient_can_view_queue_and_medical_history(): void
    {
        $patientRole = Role::where('name', 'Pasien')->first();
        $user = User::factory()->create(['role_id' => $patientRole->id]);
        $patient = Patient::create([
            'user_id' => $user->id,
            'patient_number' => 'RM-005',
            'name' => 'Pasien History',
            'nik' => '3201010101010077',
            'gender' => 'L',
            'birth_date' => '1988-08-08',
            'phone' => '081222222222',
            'address' => 'Tangerang',
            'patient_type' => 'JEMAAT',
        ]);

        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Pusing dan mual',
            'diagnosis' => 'Dispepsia',
            'treatment' => 'Antasida 3x1',
            'notes' => 'Makan teratur',
        ]);

        VitalSign::create([
            'medical_record_id' => $record->id,
            'blood_pressure' => '120/80',
            'temperature' => 36.6,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // View medical history list
        $historyResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/mobile/medical-records/my-history');

        $historyResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.diagnosis', 'Dispepsia')
            ->assertJsonPath('data.0.vital_sign.blood_pressure', '120/80');

        // View single history detail
        $singleResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/mobile/medical-records/my-history/{$record->id}");

        $singleResponse->assertStatus(200)
            ->assertJsonPath('data.id', $record->id)
            ->assertJsonPath('data.treatment', 'Antasida 3x1');
    }
}
