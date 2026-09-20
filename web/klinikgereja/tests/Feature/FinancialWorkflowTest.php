<?php

namespace Tests\Feature;

use App\Models\ChurchFund;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Subsidy;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $treasurer;
    private User $admin;
    private User $nurse;
    private Patient $patient;
    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin']);
        $treasurerRole = Role::create(['name' => 'Bendahara']);
        $nurseRole = Role::create(['name' => 'Perawat']);
        $doctorRole = Role::create(['name' => 'Dokter']);
        Role::create(['name' => 'Super Admin']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->treasurer = User::factory()->create(['role_id' => $treasurerRole->id]);
        $this->nurse = User::factory()->create(['role_id' => $nurseRole->id]);
        $doctorUser = User::factory()->create(['role_id' => $doctorRole->id]);

        $this->doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'dr. Yohanes',
            'specialization' => 'Umum',
        ]);

        $this->patient = Patient::create([
            'patient_number' => 'RM-002',
            'name' => 'Maria Kristina',
            'nik' => '3201010101010002',
            'gender' => 'P',
            'birth_date' => '1985-05-12',
            'phone' => '08123456780',
            'address' => 'Tangerang',
            'patient_type' => 'JEMAAT',
            'church_member_number' => 'GKJ-002',
        ]);
    }

    public function test_treasurer_can_manage_and_approve_subsidy(): void
    {
        $record = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Pemeriksaan tensi dan gula darah',
            'diagnosis' => 'Hipertensi',
        ]);

        // Create pending subsidy
        $response = $this->actingAs($this->admin)->post('/subsidies', [
            'patient_id' => $this->patient->id,
            'medical_record_id' => $record->id,
            'amount' => 50000,
            'date' => now()->toDateString(),
            'notes' => 'Bantuan pengobatan jemaat lanjut usia',
        ]);
        $response->assertRedirect();

        $subsidy = Subsidy::first();
        $this->assertNotNull($subsidy);
        $this->assertEquals('PENDING', $subsidy->status);
        $this->assertEquals('50000.00', $subsidy->amount);

        // Approve subsidy by treasurer
        $response = $this->actingAs($this->treasurer)->post("/subsidies/{$subsidy->id}/approve");
        $response->assertRedirect();

        $this->assertEquals('APPROVED', $subsidy->fresh()->status);
        $this->assertEquals($this->treasurer->id, $subsidy->fresh()->approved_by);
    }

    public function test_transaction_creation_and_payment_processing(): void
    {
        $record = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'complaint' => 'Konsultasi & obat',
        ]);

        // Create transaction with service cost 80.000 and subsidy 50.000 -> total payment 30.000
        $response = $this->actingAs($this->admin)->post('/transactions', [
            'patient_id' => $this->patient->id,
            'medical_record_id' => $record->id,
            'service_cost' => 80000,
            'subsidy_amount' => 50000,
            'payment_method' => 'CASH',
            'status' => 'UNPAID',
        ]);
        $response->assertRedirect();

        $transaction = Transaction::first();
        $this->assertNotNull($transaction);
        $this->assertEquals('80000.00', $transaction->service_cost);
        $this->assertEquals('50000.00', $transaction->subsidy_amount);
        $this->assertEquals('30000.00', $transaction->total_payment);
        $this->assertEquals('UNPAID', $transaction->status);

        // Pay transaction
        $response = $this->actingAs($this->treasurer)->post("/transactions/{$transaction->id}/pay", [
            'payment_method' => 'CASH',
        ]);
        $response->assertRedirect();

        $freshTx = $transaction->fresh();
        $this->assertEquals('PAID', $freshTx->status);
        $this->assertNotNull($freshTx->paid_at);
        $this->assertEquals($this->treasurer->id, $freshTx->cashier_id);
    }

    public function test_church_fund_recording_and_reports(): void
    {
        // Add fund inflow (Donation)
        $this->actingAs($this->treasurer)->post('/church-funds', [
            'type' => 'INFLOW',
            'category' => 'DONATION',
            'amount' => 1000000,
            'date' => now()->toDateString(),
            'description' => 'Persembahan kasih untuk klinik',
        ]);

        // Add fund outflow (Supplies)
        $this->actingAs($this->treasurer)->post('/church-funds', [
            'type' => 'OUTFLOW',
            'category' => 'MEDICAL_SUPPLIES',
            'amount' => 300000,
            'date' => now()->toDateString(),
            'description' => 'Pembelian obat-obatan generik',
        ]);

        $this->assertDatabaseHas('church_funds', ['type' => 'INFLOW', 'amount' => '1000000.00']);
        $this->assertDatabaseHas('church_funds', ['type' => 'OUTFLOW', 'amount' => '300000.00']);

        // View reports dashboard
        $response = $this->actingAs($this->treasurer)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('1.000.000');
        $response->assertSee('300.000');
    }

    public function test_unauthorized_roles_cannot_manage_church_funds(): void
    {
        $response = $this->actingAs($this->nurse)->get('/church-funds');
        $response->assertStatus(403);

        $response = $this->actingAs($this->nurse)->post('/church-funds', [
            'type' => 'INFLOW',
            'category' => 'DONATION',
            'amount' => 500000,
            'date' => now()->toDateString(),
            'description' => 'Test',
        ]);
        $response->assertStatus(403);
    }
}
