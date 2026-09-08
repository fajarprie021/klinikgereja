# Implementation Status — Klinik Gereja

> Tracking implementasi sesuai `development-plan.md` dan `workflow.md`.
> Format ini fokus pada fitur MVP v1.

## Legend

- **Planned**: belum dikerjakan.
- **In Progress**: sedang dikerjakan.
- **Done**: sudah selesai dan bisa dipakai.
- **Blocked**: ada kendala/ketergantungan.

## Kondisi Awal

- Saat ini: **Belum mulai (belum ada implementasi)**
- Catatan: Laravel sudah ada di proyek lain, tetapi `klinikgereja` belum punya kode Laravel sendiri.

## Phase 1 — Foundation (Target: Setup Laravel, Database, Auth, Role)

> Status tetap **Planned** sampai ada kode Laravel khusus `klinikgereja` (route/controller/model/migration).
> Catatan: Laravel sudah tersedia di proyek lain (`F:\AI\gkj_tgr_sim`, `F:\AI\gkj-laravel`). Jangan scaffold ulang; reuse pattern struktur & komponen.

### 1) Setup Project & Environment

- Status: **In Progress**
- Checklist:
  - [x] Siapkan folder Laravel khusus `klinikgereja` (baru di `f:\AI\klinikgereja\web\klinikgereja`)
  - [ ] Setup `.env` + database connection untuk klinik
  - [ ] Konvensi folder (models, controllers, policies, requests)
  - [ ] Migrations siap + mekanisme seed
  - [ ] Jangan reuse DB/auth aplikasi lain secara langsung

### 2) Authentication (Login/Logout, session)

- Status: **Done**
- Checklist:
  - [x] UI/route login berbasis `username` (GET/POST)
  - [x] Logout
  - [x] Middleware `auth`
  - [x] `User` model memakai tabel `users`
  - [x] Redirect policy setelah login

### 3) Role & Permission Management

- Status: **Done**
- Checklist:
  - [ ] Migration/model `roles`
  - [ ] Tambah kolom `role_id` pada `users` (migration)
  - [ ] Relasi Eloquent `User -> Role`
  - [ ] Gates/policies atau middleware role
  - [ ] Seeder: `Super Admin`, `Admin`, `Dokter`, `Perawat`, `Bendahara`
  - [ ] Test dasar: akses route sesuai role

## Phase 2 — Clinical Workflow (Pasien, Dokter, Jadwal, Appointment, Rekam Medis)

### 4) Patient Management

- Status: **Done**
- Business goal (MVP v1): registrasi pasien + administrasi data pasien.
- Checklist:
  - [x] Migration/model `patients`
  - [x] Validasi `patient_type` (JEMAAT/UMUM)
  - [x] Validasi `patient_number` unik
  - [x] Request validation (FormRequest)
  - [x] CRUD: routes + controller + views/API endpoints
  - [x] Authorization: role yang boleh create/update

### 5) Doctor Management

- Status: **Done**
- Business goal (MVP v1): input dan pengelolaan profil dokter.
- Checklist:
  - [x] Migration/model `doctors`
  - [x] Relasi `doctors.user_id -> users.id`
  - [x] Validasi `specialization`
  - [x] CRUD routes + controller
  - [x] Pemilihan akun dokter sesuai role `Dokter`

### 6) Schedule (Jadwal Dokter)

- Status: **In Progress**
- Business goal (MVP v1): menentukan ketersediaan layanan dokter.
- Checklist:
  - [x] Migration/model `schedules`
  - [x] Validasi `day` (enum/representasi konsisten)
  - [x] Validasi jam: `start_time < end_time`
  - [x] Validasi overlap jam dokter yang sama
  - [x] CRUD schedules (UI/endpoint)

### 7) Appointment / Antrean

- Status: **Planned**
- Business goal (MVP v1): menempatkan pasien masuk antrean untuk pemeriksaan.
- Checklist:
  - [ ] Migration/model `appointments`
  - [ ] Endpoint create appointment (pasien + dokter + date)
  - [ ] Aturan `queue_number` (unik per dokter+tanggal)
  - [ ] Status flow: WAITING → CHECKED → DONE
  - [ ] Transisi status hanya oleh role tertentu
  - [ ] Opsional: CANCELLED + aturan pembatalan

### 8) Medical Record (Rekam Medis)

- Status: **Planned**
- Business goal (MVP v1): penyimpanan diagnosis dan tindakan dokter.
- Checklist:
  - [ ] Migration/model `medical_records`
  - [ ] Relasi: patient, doctor, appointment
  - [ ] Saat status appointment CHECKED → DONE, record dibuat/di-update
  - [ ] Validasi field: complaint/diagnosis/treatment/notes
  - [ ] CRUD minimal: view per appointment/pasien (MVP)

### 9) Vital Signs (Pemeriksaan awal oleh perawat)

- Status: **Planned**
- Business goal (MVP v1): input tanda vital sebelum dokter memeriksa.
- Checklist:
  - [ ] Migration/model `vital_signs`
  - [ ] Relasi ke `medical_record_id`
  - [ ] Validasi tipe data & rentang nilai (opsional)
  - [ ] Endpoint input vital signs oleh role `Perawat`
  - [ ] Tampilkan ringkasan vital signs di halaman pemeriksaan

## Phase 3 — Financial (Transaksi, Subsidi, Dana Gereja, Laporan)

### 10) Subsidy (Subsidi Kesehatan)

- Status: **Planned**
- Business goal (MVP v1): aturan subsidi, persetujuan gereja.
- Checklist:
  - [ ] Migration/model `subsidies`
  - [ ] Validasi relasi: patient_id + medical_record_id
  - [ ] CRUD subsidi + approval UI/endpoint
  - [ ] Field `amount`, `date`, `approved_by`
  - [ ] Validasi aturan approval (hanya role Bendahara/Super Admin)
  - [ ] Audit: perubahan status (opsional)

### 11) Transactions / Pembayaran

- Status: **Planned**
- Business goal (MVP v1): hitung total biaya, simpan pembayaran pasien.
- Checklist:
  - [ ] Migration/model `transactions`
  - [ ] Endpoint membuat transaksi setelah pemeriksaan selesai
  - [ ] Hitung `service_cost` + `subsidy` → `total_payment`
  - [ ] Validasi skenario tanpa subsidy
  - [ ] Status transaksi (mis. PAID/UNPAID sesuai kebutuhan)
  - [ ] Authorization (role Admin/Bendahara sesuai proses)
  - [ ] Relasi: patient_id + medical_record_id

### 12) Church Funds (Dana Pelayanan)

- Status: **Planned**
- Business goal (MVP v1): pencatatan dana layanan & transparansi.
- Checklist:
  - [ ] Migration/model `church_funds`
  - [ ] Validasi field: `type`, `amount`, `date`, `description`
  - [ ] CRUD endpoints/UI (role: Bendahara)
  - [ ] (MVP) Gunakan data dari event pelayanan untuk konsistensi

### 13) Reports (Laporan Pelayanan Gereja)

- Status: **Planned**
- Business goal (MVP v1): dashboard + laporan gereja dari data pelayanan.
- Checklist:
  - [ ] Endpoint/dashboard ringkas
  - [ ] Report per periode (hari/minggu/bulan)
  - [ ] Export (opsional)

## Phase 4 — Mobile (di luar MVP v1, menunggu)

- Status: **Planned**
- Checklist:
  - [ ] Mobile login pasien
  - [ ] Booking
  - [ ] Antrean
  - [ ] Riwayat medis

---

## MVP Coverage Check

Fitur wajib (MVP v1): **Authentication, Role management, Patient Management, Doctor Management, Appointment, Medical Record, Subsidy, Report**

- Status saat ini: **Planned semua** (sebagian sudah setup project Laravel)\*\*

## Notes / Decision Log

- [x] Bootstrap Laravel sudah dibuat di `f:\AI\klinikgereja\web\klinikgereja`.
- [x] SQLite dipakai dulu (default Laravel).
- [ ] Implementasi Auth/Role akan lanjut setelah struktur user/role selesai.
- [ ] Tentukan lokasi kode Laravel klinik: project baru vs reuse `gkj-laravel`/`gkj_tgr_sim`.
- [ ] Sesuaikan enumerasi `patient_type` & status appointment: WAITING/CHECKED/DONE/CANCELLED
- [ ] Tentukan aturan `queue_number` (unik per `doctor_id + date` atau lainnya)
- [ ] Tentukan formula `total_payment` dan skenario tanpa subsidy
