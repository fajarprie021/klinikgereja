# Menjalankan Klinik Gereja dengan XAMPP

Aplikasi dijalankan melalui Apache XAMPP dengan PHP 8.3.35. Folder proyek tetap berada di `F:\AI\klinikgereja\web\klinikgereja`; aplikasi dapat diakses melalui `http://localhost:8000/klinikgereja/`.

## Prasyarat

- XAMPP terpasang di `C:\xampp`; PHP 8.3.35 berada di `C:\xampp\php83`.
- Composer, Node.js, dan npm.
- Jika PHP menampilkan peringatan `VCRUNTIME140.dll` tidak cocok, perbaiki Microsoft Visual C++ Redistributable 2015–2022 (x64), lalu buka PowerShell baru. Pastikan `C:\xampp\php83\php.exe -v` berjalan tanpa error sebelum melanjutkan.

## Setup pertama kali

Buka PowerShell dan arahkan CLI ke PHP XAMPP untuk sesi tersebut:

```powershell
$env:Path = "C:\xampp\php83;$env:Path"
php -v
composer --version
node --version
npm --version
cd F:\AI\klinikgereja\web\klinikgereja
```

Jika `composer` belum ditemukan, pasang Composer untuk Windows terlebih dahulu. Jalankan Composer dari PowerShell yang sama agar menggunakan PHP XAMPP.

Pasang dependensi:

```powershell
composer install
npm install
```

Siapkan `.env` dan database SQLite jika belum tersedia:

```powershell
if (!(Test-Path .env)) { Copy-Item .env.example .env }
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }
```

Pastikan pengaturan berikut ada di `.env`:

```dotenv
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Jika `APP_KEY` masih kosong, buat key aplikasi:

```powershell
php artisan key:generate
```

Buat tabel, akun awal, dan aset frontend:

```powershell
php artisan migrate --seed
npm run build
```

`migrate --seed` mempertahankan data yang ada. Jangan gunakan `migrate:fresh` pada database yang berisi data karena perintah tersebut menghapus tabel.

## Konfigurasi Apache XAMPP

Apache mendengarkan pada port 80 dan 8000. Virtual host port 8000 dan alias `/klinikgereja` berada di `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, yang menunjuk ke folder:

```text
F:\AI\klinikgereja\web\klinikgereja\public
```

Pastikan `C:\xampp\apache\conf\httpd.conf` memuat `Listen 8000` dan konfigurasi virtual host:

```apache
Include conf/extra/httpd-vhosts.conf
```

Konfigurasi XAMPP standar juga perlu memuat `httpd-xampp.conf`, yang mengaktifkan PHP Apache. Pada konfigurasi XAMPP standar, `mod_rewrite` sudah aktif dan aturan Laravel ada di `public/.htaccess`.

Pada virtual host port 8000, alias `/klinikgereja` menunjuk ke folder `public` proyek. `AllowOverride All` diperlukan agar aturan Laravel di `public/.htaccess` berfungsi. `RewriteBase /klinikgereja/` menjaga route dan aset bekerja pada sub-path tersebut.

## Menjalankan aplikasi

1. Buka **XAMPP Control Panel**. Jika Apache gagal start karena port 80 dipakai, hentikan aplikasi yang menggunakan port tersebut atau ubah port Apache.
2. Tekan **Start** pada baris **Apache**. SQLite digunakan oleh aplikasi, jadi MySQL XAMPP tidak perlu dijalankan.
3. Buka aplikasi:

- Beranda: http://localhost:8000/klinikgereja/
- Login: http://localhost:8000/klinikgereja/login

Untuk menghentikan aplikasi, tekan **Stop** pada Apache di XAMPP Control Panel.

## Akun pengujian lokal

Setelah seeder dijalankan:

- Username: `admin`
- Password: `Admin123!`

Gunakan akun ini hanya untuk pengembangan lokal. Ganti atau nonaktifkan sebelum aplikasi dipasang di lingkungan lain.

## Data dummy dan akun demo

Untuk mengisi data contoh tanpa menghapus data yang sudah ada, jalankan:

```powershell
cd F:\AI\klinikgereja\web\klinikgereja
& C:\xampp\php83\php.exe artisan db:seed --class=DemoDataSeeder
```

Seeder ini aman dijalankan berulang kali dan membuat pasien, dokter, jadwal, janji temu, rekam medis sintetis, tanda vital contoh, subsidi, transaksi, serta akun demo untuk role dokter, perawat, bendahara, dan super admin. Data pasien dan pemeriksaan sepenuhnya sintetis; jangan gunakan sebagai data pelayanan nyata.

Akun demo memakai kata sandi yang sama, `Demo123!`:

- `demo-doctor-1`, `demo-doctor-2`, `demo-doctor-3` — Dokter
- `demo-nurse` — Perawat
- `demo-treasurer` — Bendahara
- `demo-superadmin` — Super Admin

Akun dan kata sandi demo hanya untuk pengembangan lokal. Jangan gunakan atau biarkan akun ini aktif di lingkungan produksi.

## Role dan akses modul

Menu dan endpoint modul dibatasi berdasarkan role melalui policy dan middleware. Super Admin memiliki akses penuh dan dapat mengelola akun melalui menu **Pengguna**. Role lain hanya dapat membuka modul yang diizinkan untuk tugasnya; akun tanpa role tidak dapat menggunakan modul yang dibatasi.

## Pengembangan frontend

Setelah mengubah CSS atau JavaScript, bangun ulang aset:

```powershell
cd F:\AI\klinikgereja\web\klinikgereja
npm run build
```

Untuk hot reload selama pengembangan, jalankan `npm run dev` di terminal PowerShell terpisah dan biarkan terminal tetap terbuka.

## Kendala umum

- **PHP menampilkan `VCRUNTIME140.dll` mismatch:** perbarui Microsoft Visual C++ Redistributable 2015–2022 (x64), lalu cek ulang `C:\xampp\php83\php.exe -v`.
- **Apache tidak bisa start:** cek port 80 dan 8000 serta pesan di XAMPP Control Panel. Salah satu port mungkin sedang digunakan service lain.
- **Halaman menampilkan 404 atau daftar folder:** pastikan Apache memakai folder Laravel `public`, `AllowOverride All` aktif, dan `mod_rewrite` dimuat.
- **Database SQLite tidak ditemukan:** pastikan file `database\database.sqlite` ada dan `.env` menunjuk ke `database/database.sqlite`, lalu jalankan `php artisan migrate --seed`.
- **Perubahan CSS atau JavaScript tidak terlihat:** jalankan `npm run build` atau jalankan `npm run dev` selama pengembangan.
