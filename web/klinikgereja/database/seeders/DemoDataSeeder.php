<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'Demo123!';

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $roles = Role::query()->pluck('id', 'name');
        $demoUsers = [
            ['username' => 'demo-doctor-1', 'name' => 'dr. Contoh Umum', 'role' => 'Dokter'],
            ['username' => 'demo-doctor-2', 'name' => 'dr. Contoh Anak', 'role' => 'Dokter'],
            ['username' => 'demo-doctor-3', 'name' => 'dr. Contoh Gigi', 'role' => 'Dokter'],
            ['username' => 'demo-nurse', 'name' => 'Perawat Contoh', 'role' => 'Perawat'],
            ['username' => 'demo-treasurer', 'name' => 'Bendahara Contoh', 'role' => 'Bendahara'],
            ['username' => 'demo-superadmin', 'name' => 'Super Admin Contoh', 'role' => 'Super Admin'],
        ];

        $users = [];
        foreach ($demoUsers as $demoUser) {
            $users[$demoUser['username']] = User::firstOrCreate(
                ['username' => $demoUser['username']],
                [
                    'name' => $demoUser['name'],
                    'email' => null,
                    'password' => Hash::make(self::DEMO_PASSWORD),
                    'role_id' => $roles[$demoUser['role']],
                    'status' => 'ACTIVE',
                    'email_verified_at' => now(),
                ],
            );
        }

        $doctorDefinitions = [
            ['user' => 'demo-doctor-1', 'specialization' => 'Dokter Umum'],
            ['user' => 'demo-doctor-2', 'specialization' => 'Dokter Anak'],
            ['user' => 'demo-doctor-3', 'specialization' => 'Dokter Gigi'],
        ];

        $doctors = [];
        foreach ($doctorDefinitions as $definition) {
            $doctorUser = $users[$definition['user']];
            $doctors[] = Doctor::firstOrCreate(
                ['user_id' => $doctorUser->id],
                ['name' => $doctorUser->name, 'specialization' => $definition['specialization']],
            );
        }

        $patients = [];
        foreach ([
            ['DEMO-P001', 'Pasien Demo Satu', 'P', '1990-03-12', 'JEMAAT', 'DEMO-J001'],
            ['DEMO-P002', 'Pasien Demo Dua', 'L', '1985-07-24', 'UMUM', null],
            ['DEMO-P003', 'Pasien Demo Tiga', 'P', '2012-11-05', 'JEMAAT', 'DEMO-J002'],
            ['DEMO-P004', 'Pasien Demo Empat', 'L', '1978-01-19', 'UMUM', null],
            ['DEMO-P005', 'Pasien Demo Lima', 'P', '2001-09-30', 'JEMAAT', 'DEMO-J003'],
        ] as [$number, $name, $gender, $birthDate, $type, $memberNumber]) {
            $patients[] = Patient::firstOrCreate(
                ['patient_number' => $number],
                [
                    'name' => $name,
                    'nik' => null,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'phone' => null,
                    'address' => 'Alamat contoh - data dummy',
                    'patient_type' => $type,
                    'church_member_number' => $memberNumber,
                ],
            );
        }

        $appointments = [];
        foreach ($patients as $index => $patient) {
            $doctor = $doctors[$index % count($doctors)];
            $appointment = Appointment::firstOrCreate(
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                ],
                [
                    'date' => today()->subDays($index % 3),
                    'queue_number' => $index + 1,
                    'status' => $index === 0 ? 'WAITING' : 'DONE',
                ],
            );
            $appointments[] = $appointment;
        }

        $demoDoctorUsernames = array_column($doctorDefinitions, 'user');
        foreach (['Senin' => 'Monday', 'Rabu' => 'Wednesday'] as $oldDay => $day) {
            Schedule::query()
                ->where('day', $oldDay)
                ->whereHas('doctor.user', fn ($query) => $query->whereIn('username', $demoDoctorUsernames))
                ->update(['day' => $day]);
        }

        foreach ($doctors as $doctor) {
            foreach (['Monday', 'Wednesday'] as $dayIndex => $day) {
                Schedule::firstOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day' => $day,
                        'start_time' => $dayIndex === 0 ? '08:00:00' : '13:00:00',
                    ],
                    ['end_time' => $dayIndex === 0 ? '12:00:00' : '16:00:00'],
                );
            }
        }

        foreach (array_slice($appointments, 0, 3) as $index => $appointment) {
            $medicalRecord = MedicalRecord::firstOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $appointment->doctor_id,
                    'complaint' => ['Demam ringan (contoh)', 'Batuk pilek (contoh)', 'Kontrol rutin (contoh)'][$index],
                    'diagnosis' => ['Observasi umum (contoh)', 'Infeksi saluran napas ringan (contoh)', 'Kondisi stabil (contoh)'][$index],
                    'treatment' => ['Istirahat dan cukup cairan (contoh)', 'Perawatan suportif (contoh)', 'Edukasi kesehatan (contoh)'][$index],
                    'notes' => 'Catatan sintetis untuk keperluan demo; bukan rekam medis nyata.',
                ],
            );

            VitalSign::firstOrCreate(
                ['medical_record_id' => $medicalRecord->id],
                [
                    'blood_pressure' => ['120/80', '110/70', '118/76'][$index],
                    'temperature' => [36.8, 37.2, 36.6][$index],
                    'weight' => [62.0, 38.5, 55.0][$index],
                    'height' => [165.0, 142.0, 158.0][$index],
                    'notes' => 'Nilai contoh untuk demonstrasi, bukan hasil pemeriksaan nyata.',
                ],
            );
        }

        $subsidyPatient = $patients[0];
        $subsidyRecord = MedicalRecord::where('patient_id', $subsidyPatient->id)->first();
        Subsidy::firstOrCreate(
            ['patient_id' => $subsidyPatient->id, 'medical_record_id' => $subsidyRecord?->id],
            [
                'amount' => 50000,
                'date' => today()->toDateString(),
                'status' => 'APPROVED',
                'approved_by' => User::where('username', 'admin')->value('id'),
            ],
        );

        $transactionPatient = $patients[1];
        $transactionRecord = MedicalRecord::where('patient_id', $transactionPatient->id)->first();
        Transaction::firstOrCreate(
            ['patient_id' => $transactionPatient->id, 'medical_record_id' => $transactionRecord?->id],
            [
                'invoice_number' => 'DEMO-INV-0001',
                'service_cost' => 75000,
                'subsidy_amount' => 0,
                'total_payment' => 75000,
                'payment_method' => 'CASH',
                'status' => 'PAID',
                'paid_at' => now(),
                'cashier_id' => $users['demo-treasurer']->id,
                'notes' => 'Transaksi sintetis untuk keperluan demo.',
            ],
        );
    }
}
