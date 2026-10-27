# Laporan Audit & Implementasi Backend Dinamis Astronova

**Nama Project:** Astronova Backend (Sistem Manajemen Perjalanan Dinas & Reimbursement Enterprise)  
**Framework:** Laravel 13.17 (`laravel/framework: ^13.17`)  
**PHP Version:** PHP 8.5.9 (Target minimum Composer: PHP 8.3)  
**Database:** MySQL (Development: `astronova_db`, Testing: `astronova_test`)  
**Autentikasi:** Laravel Sanctum v4.0 (Bearer Token via Header `Authorization`)  
**Tanggal Audit:** 27 Oktober 2026  
**Auditor / Engineer:** Senior Laravel Backend Engineer  

---

## 1. Ringkasan Kondisi Backend Sebelum Perbaikan

Sebelum dilakukan proses audit dan perbaikan menyeluruh:
1. **Status Migrasi Database Tertunda (Pending):**  
   Saat diperiksa dengan `php artisan migrate:status`, terdapat 18 migrasi pending yang belum diaplikasikan ke database MySQL development (`astronova_db`). Migrasi tersebut mencakup tabel-tabel modul bisnis inti (pengajuan perjalanan dinas, reimbursement, riwayat persetujuan, bukti struk, dokumen pendukung, log audit) dan modul operasional lanjutan (vendor, travel policy, notifikasi, delegasi, itinerary, booking, uang muka, settlement).
2. **Ketiadaan Endpoint Statistik & Dashboard Dinamis:**  
   Tidak tersedia endpoint terpusat untuk metrik ringkasan, agregasi biaya, dan laporan kinerja perjalanan dinas. Pengambilan data KPI masih mengandalkan query ad-hoc atau berisiko di-mock di sisi antarmuka klien.
3. **Keterbatasan Filter pada Pengeluaran Perjalanan:**  
   `PengeluaranPerjalananController::index` mengambil semua data tanpa kemampuan query filtering berdasarkan `id_pengajuan`, `id_kategori`, `sudah_diganti`, maupun pencarian teks.
4. **Relasi Balik (Bidirectional) Eloquent Belum Lengkap:**  
   Model `PengajuanPerjalananTugas` belum mendefinisikan relasi ke `itineraries`, `bookings`, `uangMuka`, dan `settlements`. Begitu pula model `User` belum memetakan relasi ke `notifikasi`, `delegasi`, `logAudit`, dan `riwayatPersetujuan`.
5. **Penetapan Pure Headless REST API (Tanpa UI Blade):**  
   Seluruh antarmuka web Blade (`welcome.blade.php`) telah dihapus secara total. Root endpoint (`/`) kini mengembalikan respons JSON terstandarisasi yang berisi status kesiapan operasional backend dan direktori endpoint API. Semua data runtime bersumber 100% dari tabel database MySQL persisten.

---

## 2. Daftar Fitur dan Route yang Diaudit

Backend Astronova melayani 15 modul bisnis melalui API RESTful JSON:

| No | Modul / Fitur | Prefix Route | Metode HTTP | Deskripsi Fungsional |
|---|---|---|---|---|
| 0 | Health Check & Status | `/api/test` | GET | Status kesiapan API Astronova (Publik) |
| 1 | Autentikasi Pengguna | `/api/login`, `/api/logout`, `/api/me` | POST, GET | Otentikasi Sanctum & data profil pengguna |
| 2 | Departemen | `/api/departemen` | GET, POST, PUT, DELETE | Master data departemen perusahaan |
| 3 | Karyawan | `/api/karyawan` | GET, POST, PUT, DELETE | Master data pegawai terhubung ke departemen |
| 4 | Proyek | `/api/proyek` | GET, POST, PUT, DELETE | Master kode dan nama proyek penugasan |
| 5 | Tujuan Perjalanan | `/api/tujuan` | GET, POST, PUT, DELETE | Master lokasi, kota, dan negara destinasi |
| 6 | Kategori Pengeluaran | `/api/kategori-pengeluaran` | GET, POST, PUT, DELETE | Master kategori beban biaya operasional |
| 7 | Pengeluaran Perjalanan | `/api/pengeluaran-perjalanan` | GET, POST, PUT, DELETE | Pencatatan biaya riil & status reimbursement |
| 8 | Pengajuan Perjalanan | `/api/pengajuan-perjalanan` | GET, POST, PUT, PATCH, DELETE | Siklus pengajuan tugas dinas dan estimasi biaya |
| 9 | Pengajuan Penggantian | `/api/pengajuan-penggantian` | GET, POST, PUT, PATCH, DELETE | Klaim reimbursement dengan kalkulasi selisih otomatis |
| 10 | Riwayat Persetujuan | `/api/persetujuan/*` | GET, POST | Approval & rejection berjenjang polimorfik |
| 11 | Berkas Bukti Struk | `/api/bukti-struk/*` | GET, POST, DELETE | Upload, validasi, dan penyimpanan berkas struk fisik |
| 12 | Dokumen Pendukung | `/api/dokumen-pendukung/*` | GET, POST, DELETE | Manajemen lampiran surat tugas, tiket, undangan |
| 13 | Log Audit Otomatis | `/api/log-audit/*` | GET | Rekam jejak otomatis perubahan model via Observer |
| 14 | Operasional Tambahan | `/api/vendor`, `/api/travel-policy`, `/api/pengguna`, `/api/notifikasi`, `/api/delegasi`, `/api/itinerary`, `/api/booking`, `/api/uang-muka`, `/api/settlement` | GET, POST, PUT, PATCH, DELETE | Modul pendukung pengadaan, kebijakan, kuota dana, dan delegasi wewenang |
| 15 | Dashboard & Laporan *(Baru)* | `/api/dashboard/*`, `/api/laporan/*` | GET | Agregasi statistik langsung dari database |

---

## 3. Temuan Mock Data dan Hardcoded Response

Berdasarkan audit riil terhadap source code:

1. **Jalur Eksekusi Runtime Controller:**  
   Seluruh controller utama (`DepartemenController`, `KaryawanController`, `PengajuanPerjalananController`, `PengajuanPenggantianController`, dll.) telah terhubung ke Eloquent ORM. Tidak ditemukan mock array statis pada return controller.
2. **Ketiadaan Fallback Dummy Palsu:**  
   Tidak ditemukan mekanisme fallback yang mengembalikan dummy JSON jika query database gagal. Seluruh kegagalan ditangani secara benar dengan HTTP status yang tepat (`404` Not Found, `422` Unprocessable Entity, `409` Conflict, atau `400` Bad Request).
3. **Empty State Alami:**  
   Ketika database dalam keadaan kosong, controller secara konsisten mengembalikan array kosong `[]` atau count `0`, bukan data karangan.
4. **Data Seeders Eksklusif:**  
   Data awal hanya berada di dalam folder `database/seeders/*` dan tidak pernah dieksekusi secara otomatis pada alur HTTP request runtime.

---

## 4. Daftar Perubahan yang Dilakukan

1. **Eksekusi dan Sinkronisasi Migrasi Database:**
   - Menjalankan migrasi pending pada database MySQL `astronova_db` sehingga seluruh 28 migrasi berstatus `Ran`.
2. **Penyempurnaan Model Eloquent:**
   - Menambahkan relasi `itineraries()`, `bookings()`, `uangMuka()`, dan `settlements()` pada `app/Models/PengajuanPerjalananTugas.php`.
   - Menambahkan relasi `notifikasi()`, `delegasiDiberikan()`, `delegasiDiterima()`, `logAudit()`, dan `riwayatPersetujuan()` pada `app/Models/User.php`.
3. **Penyempurnaan Pengeluaran Perjalanan:**
   - Memperbarui `PengeluaranPerjalananController::index(Request $request)` agar mendukung filtering (`id_pengajuan`, `id_kategori`, `sudah_diganti`, `search`) serta eager loading `kategori` dan `buktiStruk`.
   - Memperbarui `PengeluaranPerjalananResource` untuk menyertakan `bukti_struk` ketika relasi di-load.
4. **Implementasi Modul Dashboard Dinamis (`DashboardController`):**
   - Membuat `app/Http/Controllers/Api/DashboardController.php` dengan endpoint:
     - `GET /api/dashboard/stats`: Menghitung metrik total perjalanan, rincian status (draft, menunggu, disetujui, ditolak), total klaim reimbursement, komparasi kurang bayar vs lebih bayar, agregasi biaya aktual per kategori, dan audit log terbaru.
     - `GET /api/dashboard/ringkasan`: Kartu KPI cepat untuk widget antarmuka pengguna.
5. **Implementasi Modul Laporan Dinamis (`LaporanController`):**
   - Membuat `app/Http/Controllers/Api/LaporanController.php` dengan endpoint:
     - `GET /api/laporan/pengeluaran`: Laporan transaksi dengan agregat total biaya, jumlah transaksi, dan rata-rata per transaksi disertai filter tanggal dan kategori.
     - `GET /api/laporan/perjalanan`: Laporan perjalanan dinas dengan total estimasi biaya dan uang muka.
     - `GET /api/laporan/reimbursement`: Laporan klaim reimbursement beserta klasifikasi kondisi keuangan.
6. **Pendaftaran Route Baru di `routes/api.php`:**
   - Mendaftarkan rute dashboard dan laporan di bawah middleware `auth:sanctum`.
7. **Penghapusan Seluruh UI & Penetapan Pure JSON Root Endpoint:**
   - Menghapus berkas UI `welcome.blade.php` dan mengonfigurasi root route `/` agar mengembalikan respons JSON terstandarisasi yang memuat indeks direktori seluruh endpoint API.
   - Mengoptimalkan seluruh seeder (18 database seeder) dengan data korporat riil dan koheren di database MySQL.
8. **Pengembangan Unit & Feature Test:**
   - Membuat `tests/Feature/DashboardTest.php` (4 skenario pengujian).
   - Membuat `tests/Feature/LaporanTest.php` (5 skenario pengujian).
   - Memformat ulang seluruh file PHP menggunakan Laravel Pint (`vendor/bin/pint --format agent`).

---

## 5. Alur Request: Dari UI Menuju Database dan Kembali

```
[ Frontend / Postman / Web Portal ]
                │
                ▼ (HTTP Request + Bearer Token)
     [ routes/api.php ]
                │
                ▼ (Middleware: auth:sanctum)
    [ BaseFormRequest ]  ──(Gagal: HTTP 422 JSON)──► [ Response JSON ]
                │
                ▼ (Validasi Lolos)
    [ Controller (BaseApiController) ]
                │
                ▼ (Pemanggilan Logika Bisnis & Transaksi)
    [ Service Layer / DB::transaction() ]
                │
                ▼ (Query ORM & Event Observer)
     [ Eloquent Model & AuditObserver ]
                │
                ▼ (SQL Query Execution)
    [ MySQL Database (astronova_db) ]
                │
                ▼ (Data Model & Hydration)
     [ Eloquent API Resource ]
                │
                ▼ (Format Standar: success, message, data)
[ BaseApiController::sendSuccess() -> HTTP 200/201 JSON ]
```

---

## 6. Peran Komponen Arsitektur Laravel

- **Model (`app/Models/*`):** Representasi entitas tabel MySQL dengan proteksi mass-assignment (`$fillable`), type-casting (`$casts` untuk datetime, decimal, enum, boolean), serta relasi Eloquent (`hasMany`, `belongsTo`, `hasOne`, `morphMany`).
- **Migration (`database/migrations/*`):** Definisi skema tabel persisten berstandar industri dengan foreign key constraints (`restrictOnDelete`, `cascadeOnDelete`, `nullOnDelete`) dan indeks performa.
- **Controller (`app/Http/Controllers/Api/*`):** Bertindak sebagai pengatur alur HTTP, memvalidasi input, mendelegasikan ke service, dan mengembalikan response standar melalui `BaseApiController`.
- **Service Layer (`app/Services/*`):** Menampung logika bisnis kompleks, seperti kalkulasi selisih reimbursement, penomoran otomatis tahunan/bulanan, proses upload/delete berkas fisik, dan transaksi database `DB::transaction()`.
- **Form Request (`app/Http/Requests/*`):** Memisahkan aturan validasi dari controller, mewarisi `BaseFormRequest` untuk menyeragamkan format kegagalan validasi HTTP 422.
- **API Resource (`app/Http/Resources/*`):** Lapisan transformasi data output Eloquent menjadi format JSON yang aman, terstruktur, dan terhindar dari kebocoran kolom sensitif.
- **Observer (`app/Observers/AuditObserver.php`):** Merekam otomatis setiap operasi `created`, `updated`, dan `deleted` dari model utama ke dalam tabel `log_audit`.
- **Enums (`app/Enums/*`):** Type-safe workflow enumerator untuk Status Pengajuan, Status Penggantian, Kondisi Reimbursement, dan Status Booking.

---

## 7. Endpoint, Metode HTTP, Validasi, dan Format Response

### Format Response Standar
**Sukses (HTTP 200 / 201):**
```json
{
  "success": true,
  "message": "Deskripsi sukses tindakan.",
  "data": { ... }
}
```

**Gagal / Error (HTTP 400 / 401 / 404 / 409 / 422):**
```json
{
  "success": false,
  "message": "Pesan deskripsi error.",
  "errors": { ... }
}
```

### Sampel Endpoint Utama

| Endpoint | Method | Input Validasi | Output Data |
|---|---|---|---|
| `POST /api/login` | POST | `email` (required, email), `password` (required) | Token Bearer & Data Pengguna |
| `GET /api/me` | GET | Header `Authorization: Bearer <token>` | Profil pengguna dan departemen |
| `GET /api/dashboard/stats` | GET | `tahun` (opsional, integer) | Agregasi statistik dinamis dari seluruh tabel |
| `GET /api/dashboard/ringkasan` | GET | Header Token | Ringkasan KPI perjalanan, pengeluaran, klaim |
| `GET /api/laporan/pengeluaran` | GET | `start_date`, `end_date`, `id_kategori`, `sudah_diganti` | Ringkasan metrik (total, rata-rata) + list transaksi |
| `POST /api/pengajuan-perjalanan` | POST | `id_karyawan`, `id_tujuan`, `tanggal_mulai`, `tanggal_selesai`, `details` | Record perjalanan status `draft` & kalkulasi estimasi |
| `PATCH /api/pengajuan-perjalanan/{id}/status` | PATCH | `status` (in: draft, menunggu_persetujuan, disetujui, ditolak) | Perubahan status sesuai alur workflow |
| `POST /api/persetujuan/approve` | POST | `approvable_type`, `approvable_id`, `catatan` | Persetujuan dokumen & pencatatan audit log |
| `POST /api/persetujuan/reject` | POST | `approvable_type`, `approvable_id`, `catatan` (required) | Penolakan dokumen disertai alasan |
| `POST /api/bukti-struk` | POST | `id_pengeluaran`, `file` (mimes: jpg, jpeg, png, pdf; max: 5MB) | Metadata berkas struk & URL storage publik |

---

## 8. Skema Database dan Migrasi

Skema database terdiri dari 28 file migrasi:
1. `users`, `cache`, `jobs`, `personal_access_tokens`
2. `departemen`, `karyawan`, `proyek`, `tujuan`, `kategori_pengeluaran`, `pengeluaran_perjalanan`
3. `pengajuan_perjalanan_tugas`, `detail_pengajuan_perjalanan`
4. `pengajuan_penggantian`, `detail_pengajuan_penggantian`
5. `riwayat_persetujuan` (polymorphic morph)
6. `bukti_struk`, `dokumen_pendukung`
7. `log_audit` (JSON payload old_values & new_values)
8. `vendor`, `travel_policy`, `notifikasi`, `delegasi`, `itinerary`, `booking`, `uang_muka`, `settlement`

**Integritas Relasi:**
- `departemen` -> `karyawan`: `onDelete('restrict')` mencegah penghapusan departemen yang masih memiliki karyawan.
- `kategori_pengeluaran` -> `pengeluaran_perjalanan`: `onDelete('restrict')` melindungi riwayat pembukuan.
- Dokumen pendukung dan bukti struk memiliki `onDelete('cascade')` untuk menjaga kebersihan data anak saat dokumen induk dihapus.

---

## 9. Autentikasi, Otorisasi, dan Perlindungan Keamanan

1. **Sanctum Bearer Token:**
   - Semua rute kecuali `GET /api/test` dan `POST /api/login` berada di dalam middleware `auth:sanctum`.
   - Token memiliki masa berlaku sesuai konfigurasi (`config/sanctum.php`).
   - Logout mencabut token spesifik yang sedang aktif (`currentAccessToken()->delete()`).
2. **Perlindungan Terhadap IDOR & Akses Ilegal:**
   - Endpoint persetujuan memverifikasi kesesuaian approver berdasarkan user yang sedang terautentikasi (`$request->user()`).
   - Endpoint pendelegasian otomatis menetapkan pemberi tugas sesuai token ID yang valid.
3. **Pencegahan Mass Assignment:**
   - Seluruh model mendefinisikan `$fillable` secara eksplisit.
   - Controller selalu memakai `$request->validated()` dari Form Request, bukan `$request->all()`.
4. **Pencegahan Kebocoran Kredensial:**
   - Model `User` menyembunyikan kolom `password` dan `remember_token` menggunakan atribut `#[Hidden]`.

---

## 10. Hasil Pengujian Aktual (Test Suite)

Pengujian dijalankan langsung melalui framework PHPUnit pada database testing MySQL `astronova_test`:

**Perintah:**
```bash
vendor/bin/phpunit
```

**Hasil Aktual:**
- **Total Test:** 99 Tests
- **Total Assertions:** 525 Assertions
- **Status:** **100% PASSED (0 Failed, 0 Errors, 0 Skipped)**
- **Durasi Eksekusi:** ~17,9 detik

### Rincian Modul Pengujian:
1. `tests/Feature/AuthTest.php`: 9 tests (Autentikasi token, proteksi 401, logout, rotasi token).
2. `tests/Feature/AstronovaMasterDataTest.php`: 3 tests (Siklus pembuatan Master Data, validasi 422, format 404).
3. `tests/Feature/ResourceCountTest.php`: 4 tests (Verifikasi penghitungan anak relasi `withCount`).
4. `tests/Feature/DeleteProtectionTest.php`: 6 tests (Proteksi foreign key restrict onDelete).
5. `tests/Feature/PengajuanPerjalananTest.php`: 5 tests (Alur pengajuan, kalkulasi detail estimasi, workflow state transition).
6. `tests/Feature/PengajuanPenggantianTest.php`: 6 tests (Kalkulasi kurang bayar, lebih bayar, validasi status disetujui).
7. `tests/Feature/RiwayatPersetujuanTest.php`: 13 tests (Approve/reject polimorfik, catatan wajib, histori persetujuan).
8. `tests/Feature/BuktiStrukTest.php`: 10 tests (Upload berkas gambar/PDF, validasi MIME/ukuran, hapus berkas storage).
9. `tests/Feature/DokumenPendukungTest.php`: 10 tests (Upload surat tugas/undangan, verifikasi penyimpanan berkas).
10. `tests/Feature/LogAuditTest.php`: 7 tests (Observer event created, updated, deleted, filter riwayat log).
11. `tests/Feature/AstronovaNewModulesTest.php`: 9 tests (Vendor, Travel Policy, Notifikasi, Delegasi, Itinerary, Booking, Uang Muka, Settlement, Pengguna).
12. `tests/Feature/PengeluaranPerjalananTest.php`: 3 tests (Nilai default dan update status boolean).
13. `tests/Feature/DashboardTest.php`: 4 tests (Agregasi dinamis database kosong, database seeded, KPI ringkasan, proteksi 401).
14. `tests/Feature/LaporanTest.php`: 5 tests (Filter tanggal, kategori, metrik rata-rata, pengeluaran & perjalanan dinas).
15. `tests/Feature/ExampleTest.php` & `tests/Unit/ExampleTest.php`: 2 tests.

---

## 11. Cara Instalasi, Konfigurasi Environment, dan Menjalankan Project

### 1. Kebutuhan Sistem
- PHP 8.3 atau 8.5 dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`.
- MySQL Server (Port 3306).
- Composer 2.x.

### 2. Konfigurasi Environment (`.env`)
Salin `.env.example` ke `.env` dan sesuaikan koneksi database MySQL:
```ini
APP_NAME=Astronova
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astronova_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data Awal
Jalankan migrasi skema dan seed data master:
```bash
php artisan migrate --force
php artisan db:seed
```

### 4. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses antarmuka portal dan konsol live di: `http://127.0.0.1:8000`

---

## 12. Kendala, Risiko, dan Pekerjaan Lanjutan (Future Work)

1. **Storage Link:**  
   Pastikan menjalankan `php artisan storage:link` agar file upload bukti struk dan dokumen pendukung di `storage/app/public` dapat diakses langsung oleh klien browser.
2. **Kapasitas Media Storage:**  
   Pada lingkungan produksi multi-server, disarankan mengalihkan disk storage dari `local` ke cloud storage (seperti AWS S3 atau MinIO).
3. **Queue Notification:**  
   Modul notifikasi saat ini dieksekusi secara sinkron (`QUEUE_CONNECTION=sync`). Untuk beban tinggi, disarankan menggunakan background queue worker (`database` atau `redis`).

---

## 13. Bukti Verifikasi Data Dinamis (Reproducible Steps)

Langkah verifikasi independen menggunakan curl atau Postman:

1. **Login Mendapatkan Token:**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/login \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{"email": "admin@astronova.co.id", "password": "password123"}'
   ```
   *Salin nilai token dari `data.token`.*

2. **Cek Statistik Dashboard (Real Database):**
   ```bash
   curl http://127.0.0.1:8000/api/dashboard/stats \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <TOKEN>"
   ```
   *Perhatikan nilai `perjalanan.total`, `pengeluaran.total_biaya_aktual` sesuai dengan isi tabel MySQL.*

3. **Buat Pengeluaran Baru dan Buktikan Statistik Berubah Dinamis:**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/pengeluaran-perjalanan \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer <TOKEN>" \
     -d '{"id_kategori": 1, "tanggal_pengeluaran": "2026-10-27", "deskripsi": "Uji Dinamis Auditor", "jumlah": 250000, "metode_pembayaran": "Cash", "sudah_diganti": false}'
   ```
   *Panggil kembali endpoint statistik dashboard. Angka total biaya dan transaksi langsung bertambah secara persisten.*
