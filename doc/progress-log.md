# Progress & Activity Log — Klinik Gereja

Dokumen ini mencatat riwayat kemajuan, penanggalan pengerjaan fitur, serta status perkembangan sistem Klinik Gereja secara kronologis.

---

## Ringkasan Kronologis Perkembangan

| Tanggal | Commit / Tag | Modul / Fitur | Status | Keterangan Singkat |
| :--- | :--- | :--- | :---: | :--- |
| **2026-09-07** | `34a24eb` | **Project Initialization** | Done | Inisialisasi struktur dokumen perancangan dan project Laravel `web/klinikgereja`. |
| **2026-09-07** | `ea6dcbf` | **Doctor Management** | Done | Pembuatan model, migration, FormRequest, policy, controller, dan view dokter. |
| **2026-09-08** | `953133b` | **Doctor Schedules** | Done | Implementasi jadwal dokter, validasi overlap jam, dan feature test. |
| **2026-09-08** | `fabd16e` | **Local Admin Seeder** | Done | Penambahan seeder akun admin default lokal. |
| **2026-09-08** | `37d6be0` | **Authentication (Username)** | Done | Migrasi login & registrasi dari email ke username beserta unit/feature test. |
| **2026-09-08** | `bd131e3` | **Appointment Queue** | Done | Implementasi sistem antrean per dokter & tanggal, status flow (WAITING/CHECKED/DONE/CANCELLED). |
| **2026-09-20** | - | **Role & Permission Enhancement** | Done | Penyempurnaan RoleMiddleware multi-role & Super Admin, helper role di User model, dan suite test otomatis `RoleTest`. |
| **2026-09-20** | - | **Medical Records & Vital Signs** | Done | Implementasi modul rekam medis, pemeriksaan tanda vital, relasi antrean, dan suite test otomatis `MedicalRecordTest`. |
| **2026-09-20** | - | **Financial & Reporting Workflow** | Done | Implementasi subsidi kesehatan, transaksi pembayaran pasien (kwitansi), kas dana gereja, dashboard laporan pelayanan, dan suite test otomatis `FinancialWorkflowTest`. |
| **2026-09-20** | - | **Repository Synchronization & Docs** | Done | Sinkronisasi repositori lokal dengan GitHub origin, penambahan tracking tanggal progress. |

---

## Log Detail Per Tanggal

### 📅 2026-09-07
- **Inisialisasi Project & Dokumentasi (`34a24eb`)**
  - Pembuatan blueprint spesifikasi, skema database (`doc/database.md`), alur kerja (`doc/workflow.md`), dan rencana pengembangan (`doc/development-plan.md`).
  - Scaffolding awal aplikasi Laravel di `web/klinikgereja`.
- **Manajemen Pasien (`Patient Management`)**
  - Model & migrasi tabel `patients`.
  - Validasi tipe pasien (`JEMAAT` / `UMUM`) dan nomor rekam medis/pasien.
  - FormRequest, Controller CRUD, dan Blade views.
- **Manajemen Dokter (`Doctor Management`) (`ea6dcbf`)**
  - Relasi `doctors.user_id -> users.id`.
  - Input & validasi spesialisasi dokter.
  - Policy otorisasi aksi dokter (`DoctorPolicy`).

---

### 📅 2026-09-08
- **Jadwal Dokter (`Doctor Schedules`) (`953133b`)**
  - Pembuatan tabel `schedules` untuk dokter.
  - Validasi hari dan jam (`start_time < end_time`) serta deteksi jadwal yang bentrok (overlap).
  - Feature test otomatis `ScheduleTest`.
- **Seeder & Autentikasi Pengguna (`fabd16e`, `37d6be0`)**
  - Penambahan kolom `username` di tabel `users`.
  - Seeder akun admin bawaan (`DatabaseSeeder`).
  - Pembaruan alur login, registrasi, dan test otentikasi.
- **Sistem Antrean & Appointment (`bd131e3`)**
  - Pembuatan tabel `appointments` dengan nomor antrean otomatis per dokter & tanggal.
  - Status flow: `WAITING` → `CHECKED` → `DONE` / `CANCELLED`.
  - Otorisasi pergantian status antrean (`AppointmentPolicy`).
  - Feature test otomatis `AppointmentTest`.

---

### 📅 2026-09-20
- **Alur Keuangan & Laporan (`Financial & Reporting Workflow`)**
  - Pembuatan tabel `subsidies`, `transactions`, dan `church_funds`.
  - Alur Subsidi Kesehatan: Form pengajuan, verifikasi status (`PENDING`/`APPROVED`/`REJECTED`), otorisasi oleh role `Bendahara` / `Super Admin`.
  - Alur Transaksi & Kasir: Perhitungan otomatis tagihan pasien `total_payment = max(0, service_cost - subsidy_amount)`, cetak kwitansi, status pembayaran (`UNPAID`/`PAID`).
  - Kas Dana Pelayanan Gereja (`church_funds`): Pencatatan arus kas masuk/keluar, kategori kas, dan kalkulasi saldo kas.
  - Dashboard Laporan Pelayanan Terpadu (`reports`): Filter rentang tanggal, statistik pasien jemaat vs umum, kunjungan antrean, nilai subsidi, dan aktivitas pelayanan dokter.
  - Feature test otomatis `FinancialWorkflowTest` (lulus pengujian).
- **Rekam Medis & Tanda Vital (`Medical Records & Vital Signs`)**
  - Pembuatan tabel `medical_records` dan `vital_signs`.
  - Integrasi relasi `Patient -> MedicalRecord`, `Doctor -> MedicalRecord`, `Appointment -> MedicalRecord`, dan `MedicalRecord -> VitalSign`.
  - Implementasi Controller, FormRequest (`StoreMedicalRecordRequest`, `UpdateMedicalRecordRequest`, `StoreVitalSignRequest`), Policy (`MedicalRecordPolicy`, `VitalSignPolicy`).
  - Otomatisasi transisi status antrean appointment menjadi `DONE` saat rekam medis disimpan.
  - UI Blade: form pemeriksaan, integrasi tanda vital, daftar rekam medis dengan pencarian/filter, dan detail pemeriksaan.
  - Feature test otomatis `MedicalRecordTest` (lulus pengujian).
- **Role & Permission Enhancement**
  - Pembaruan `User` model dengan helper method role (`isSuperAdmin`, `isAdmin`, `isDoctor`, `isNurse`, `isTreasurer`, dan dukungan multi-role pada `hasRole`).
  - Pembaruan `RoleMiddleware` untuk mendukung multiple roles (pemisah koma / pipe) serta bypass untuk Super Admin.
  - Implementasi hook `before` pada seluruh Policy (`PatientPolicy`, `DoctorPolicy`, `SchedulePolicy`, `AppointmentPolicy`) untuk Super Admin.
  - Penambahan feature test `RoleTest` (memverifikasi seeder 5 role, helper methods, dan pembatasan akses route).
- **Sinkronisasi & Dokumentasi Log**
  - Penataan dan sinkronisasi workspace Git lokal dengan remote GitHub (`origin/main`).
  - Pembuatan dokumen `doc/progress-log.md` dan pembaruan tanggal status pada `doc/implementation-status.md`.

---

## Rencana Tahapan Selanjutnya (Roadmap Phase 4 - Mobile)

| Target Waktu | Modul | Deskripsi Pekerjaan | Status |
| :--- | :--- | :--- | :---: |
| *Future Phase* | **Mobile Auth** | Login & verifikasi akun pasien via aplikasi mobile. | Planned |
| *Future Phase* | **Mobile Booking** | Pendaftaran antrean dan booking jadwal dokter secara mandiri oleh pasien. | Planned |
| *Future Phase* | **Live Queue Tracker** | Monitoring nomor antrean aktif secara real-time dari aplikasi mobile. | Planned |
| *Future Phase* | **Patient Medical History** | Akses riwayat pemeriksaan medis dan resep obat secara digital oleh pasien. | Planned |
