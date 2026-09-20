# Implementation Status — Klinik Gereja

> Tracking implementasi sesuai `development-plan.md` dan `workflow.md`.  
> Log riwayat penanggalan detail dapat dilihat di [`doc/progress-log.md`](./progress-log.md).  
> **Terakhir Diperbarui:** 2026-09-20

## Legend

- **Planned**: belum dikerjakan.
- **In Progress**: sedang dikerjakan.
- **Done**: sudah selesai dan bisa dipakai.
- **Blocked**: ada kendala/ketergantungan.

## Kondisi Terkini (per 2026-09-20)

- **Fase 1 (Foundation)**: Setup Laravel, Autentikasi Username, Seeder Admin, dan Role & Permission Management telah selesai.
- **Fase 2 (Clinical Workflow)**: Modul Pasien, Dokter, Jadwal Dokter, Antrean/Appointment, Rekam Medis (Medical Records), dan Tanda Vital (Vital Signs) telah selesai diimplementasikan dan diuji.
- **Fase 3 (Financial Workflow)**: Modul Subsidi Kasih, Transaksi & Kasir, Kas Dana Pelayanan Gereja, dan Dashboard Laporan telah selesai diimplementasikan dan diuji.
- **Fase 4 (Mobile & REST API)**: Seluruh REST API endpoint untuk aplikasi mobile pasien (Registrasi/Login Sanctum, Booking Antrean Mandiri, Live Queue Tracker, dan Riwayat Rekam Medis) telah selesai diimplementasikan dan diuji 100%.

## Phase 1 — Foundation (Target: Setup Laravel, Database, Auth, Role)

### 1) Setup Project & Environment

- Status: **Done** *(2026-09-07)*
- Checklist:
  - [x] Siapkan folder Laravel khusus `klinikgereja` (`web/klinikgereja`)
  - [x] Setup `.env` + database connection untuk klinik
  - [x] Konvensi folder (models, controllers, policies, requests)
  - [x] Migrations siap + mekanisme seed
  - [x] Jangan reuse DB/auth aplikasi lain secara langsung

### 2) Authentication (Login/Logout, session)

- Status: **Done** *(2026-09-08)*
- Checklist:
  - [x] UI/route login berbasis `username` (GET/POST)
  - [x] Logout
  - [x] Middleware `auth`
  - [x] `User` model memakai tabel `users`
  - [x] Redirect policy setelah login

### 3) Role & Permission Management

- Status: **Done** *(2026-09-20)*
- Checklist:
  - [x] Migration/model `roles`
  - [x] Tambah kolom `role_id` pada `users` (migration)
  - [x] Relasi Eloquent `User -> Role`
  - [x] Gates/policies atau middleware role (Policy terpasang di Doctor, Schedule, Appointment, RoleMiddleware multi-role & Super Admin bypass)
  - [x] Seeder: `Super Admin`, `Admin`, `Dokter`, `Perawat`, `Bendahara`
  - [x] Test dasar: akses route sesuai role (`RoleTest`)

## Phase 2 — Clinical Workflow (Pasien, Dokter, Jadwal, Appointment, Rekam Medis)

### 4) Patient Management

- Status: **Done** *(2026-09-07)*
- Business goal (MVP v1): registrasi pasien + administrasi data pasien.
- Checklist:
  - [x] Migration/model `patients`
  - [x] Validasi `patient_type` (JEMAAT/UMUM)
  - [x] Validasi `patient_number` unik
  - [x] Request validation (FormRequest)
  - [x] CRUD: routes + controller + views/API endpoints
  - [x] Authorization: role yang boleh create/update

### 5) Doctor Management

- Status: **Done** *(2026-09-07)*
- Business goal (MVP v1): input dan pengelolaan profil dokter.
- Checklist:
  - [x] Migration/model `doctors`
  - [x] Relasi `doctors.user_id -> users.id`
  - [x] Validasi `specialization`
  - [x] CRUD routes + controller
  - [x] Pemilihan akun dokter sesuai role `Dokter`

### 6) Schedule (Jadwal Dokter)

- Status: **Done** *(2026-09-08)*
- Business goal (MVP v1): menentukan ketersediaan layanan dokter.
- Checklist:
  - [x] Migration/model `schedules`
  - [x] Validasi `day` (enum/representasi konsisten)
  - [x] Validasi jam: `start_time < end_time`
  - [x] Validasi overlap jam dokter yang sama
  - [x] CRUD schedules (UI/endpoint)

### 7) Appointment / Antrean

- Status: **Done** *(2026-09-08)*
- Business goal (MVP v1): menempatkan pasien masuk antrean untuk pemeriksaan.
- Checklist:
  - [x] Migration/model `appointments`
  - [x] Endpoint create appointment (pasien + dokter + date)
  - [x] Aturan `queue_number` (unik per dokter+tanggal)
  - [x] Status flow: WAITING → CHECKED → DONE
  - [x] Transisi status hanya oleh role tertentu
  - [x] CANCELLED + aturan pembatalan

### 8) Medical Record (Rekam Medis)

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): penyimpanan diagnosis dan tindakan dokter.
- Checklist:
  - [x] Migration/model `medical_records`
  - [x] Relasi: patient, doctor, appointment, vitalSign
  - [x] Saat status appointment CHECKED → DONE, record dibuat/di-update
  - [x] Validasi field: complaint/diagnosis/treatment/notes
  - [x] CRUD: routes + controller + views/API endpoints + search filter

### 9) Vital Signs (Pemeriksaan awal oleh perawat)

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): input tanda vital sebelum dokter memeriksa.
- Checklist:
  - [x] Migration/model `vital_signs`
  - [x] Relasi ke `medical_record_id`
  - [x] Validasi tipe data & rentang nilai (tekanan darah, suhu, berat, tinggi, nadi, pernapasan)
  - [x] Endpoint input vital signs oleh role `Perawat` & terintegrasi di form rekam medis
  - [x] Tampilkan ringkasan vital signs di halaman pemeriksaan / show rekam medis

## Phase 3 — Financial (Transaksi, Subsidi, Dana Gereja, Laporan)

### 10) Subsidy (Subsidi Kesehatan)

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): aturan subsidi, persetujuan gereja.
- Checklist:
  - [x] Migration/model `subsidies`
  - [x] Validasi relasi: patient_id + medical_record_id
  - [x] CRUD subsidi + approval UI/endpoint (approve/reject)
  - [x] Field `amount`, `date`, `approved_by`
  - [x] Validasi aturan approval (hanya role Bendahara/Super Admin)
  - [x] Audit/Status: PENDING, APPROVED, REJECTED

### 11) Transactions / Pembayaran

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): hitung total biaya, simpan pembayaran pasien.
- Checklist:
  - [x] Migration/model `transactions`
  - [x] Endpoint membuat transaksi setelah pemeriksaan selesai
  - [x] Hitung `service_cost` - `subsidy` → `total_payment` (minimum 0)
  - [x] Validasi skenario tanpa subsidy dan subsidi penuh
  - [x] Status transaksi: UNPAID, PAID, CANCELLED
  - [x] Authorization (role Admin/Bendahara)
  - [x] Relasi: patient_id + medical_record_id + cetak kwitansi

### 12) Church Funds (Dana Pelayanan)

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): pencatatan dana layanan & transparansi.
- Checklist:
  - [x] Migration/model `church_funds`
  - [x] Validasi field: `type` (INFLOW/OUTFLOW), `category`, `amount`, `date`, `description`
  - [x] CRUD endpoints/UI (role: Bendahara)
  - [x] Perhitungan ringkasan saldo kas pelayanan secara otomatis

### 13) Reports (Laporan Pelayanan Gereja)

- Status: **Done** *(2026-09-20)*
- Business goal (MVP v1): dashboard + laporan gereja dari data pelayanan.
- Checklist:
  - [x] Endpoint/dashboard ringkas laporan terpadu
  - [x] Filter report per periode tanggal
  - [x] Statistik pelayanan pasien (Jemaat vs Umum), kunjungan dokter, nilai subsidi, kas masuk & saldo gereja
  - [x] Cetak / print laporan

## Phase 4 — Mobile & REST API

- Status: **Done** *(2026-09-20)*
- Checklist:
  - [x] Autentikasi Sanctum & registrasi/login pasien mobile (`/api/v1/mobile/auth/*`)
  - [x] Booking jadwal & antrean dokter mandiri (`/api/v1/mobile/appointments/book`)
  - [x] Live Queue Tracker & monitoring nomor antrean aktif (`/api/v1/mobile/doctors/{doctor}/live-queue`)
  - [x] Akses riwayat rekam medis digital pasien (`/api/v1/mobile/medical-records/my-history`)
  - [x] Automated feature test suite `MobileApiTest`

---

## MVP Coverage Check (per 2026-09-20)

Fitur wajib (MVP v1 & Mobile API): **Authentication, Role management, Patient Management, Doctor Management, Appointment, Medical Record, Subsidy, Report, Mobile REST API**

- **Selesai (Done - 100% Seluruh Fase 1 s/d 4 Selesai)**:
  - ✅ Authentication (Username based) & Role Management (5 Role + Super Admin)
  - ✅ Patient Management (Jemaat & Umum)
  - ✅ Doctor Management & Doctor Schedules (Validasi overlap jam)
  - ✅ Appointment Queue Flow (WAITING → CHECKED → DONE)
  - ✅ Medical Record & Vital Signs (Pemeriksaan dokter & perawat)
  - ✅ Subsidi Kasih Gereja (Pengajuan & Persetujuan Bendahara)
  - ✅ Transaksi & Pembayaran Kasir (Kwitansi & perhitungan subsidi)
  - ✅ Kas Dana Pelayanan Gereja (Arus kas masuk & keluar)
  - ✅ Laporan & Dashboard Pelayanan Gereja
  - ✅ Mobile REST API (Sanctum Auth, Mobile Booking, Live Queue, Riwayat Medis)

## Notes / Decision Log

- [x] Bootstrap Laravel sudah dibuat di `web/klinikgereja` (2026-09-07).
- [x] SQLite dipakai dulu sebagai default database (2026-09-07).
- [x] Autentikasi berbasis `username` selesai diimplementasikan (2026-09-08).
- [x] Modul Antrean & Appointment dokter selesai dengan validasi urutan kuota (2026-09-08).
- [x] Role & Permission Management disempurnakan dengan 5 role dan Super Admin bypass (2026-09-20).
- [x] Modul Rekam Medis (Medical Records) dan Tanda Vital (Vital Signs) selesai diimplementasikan dengan otomatisasi transisi status appointment (2026-09-20).
- [x] Modul Keuangan (Subsidi Kasih, Transaksi & Kasir, Kas Gereja, Dashboard Laporan) selesai diimplementasikan dan diuji (2026-09-20).
- [x] Mobile REST API Backend (Laravel Sanctum, Booking Antrean, Live Queue Tracker, Riwayat Medis) selesai diimplementasikan dan diuji (2026-09-20).
