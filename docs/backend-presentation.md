# Materi Presentasi Backend: Sistem Manajemen Perjalanan Dinas & Reimbursement PT Astronova

Presentasi ini dirancang khusus untuk audience siswa SMK Rekayasa Perangkat Lunak (RPL) dan reviewer teknis, dengan bahasa yang lugas, terstruktur, berbasis implementasi kode nyata (tanpa mock data).

---

## Slide 1: Judul Project & Tujuan Backend

### Konten Slide:
- **Judul:** Bedah Arsitektur Backend Dinamis: PT Astronova
- **Subjudul:** Sistem Manajemen Perjalanan Dinas, Reimbursement, & Kontrol Anggaran Berbasis Laravel 13
- **Teknologi Utama:** PHP 8.5 / Laravel 13 / MySQL / Laravel Sanctum
- **Tujuan Utama Backend:**
  - Menyediakan RESTful API yang andal, aman, dan berstandar industri.
  - Mengelola data secara 100% dinamis dan persisten pada database MySQL.
  - Menghilangkan mock data, fake response, dan fallback palsu dari alur runtime sistem.

> **Catatan Pembicara (Speaker Notes):**  
> *"Halo semuanya! Selamat datang di sesi presentasi arsitektur backend project Astronova. Project ini adalah aplikasi enterprise untuk mengelola perjalanan dinas dan reimbursement karyawan. Hari ini kita akan membongkar bagaimana backend Laravel dibangun dari nol secara profesional: dari relasi tabel di database, validasi input, autentikasi token, hingga pembuktian bahwa seluruh data berjalan dinamis tanpa data palsu atau dummy hardcoded."*

---

## Slide 2: Gambaran Arsitektur Project Laravel

### Konten Slide:
- **Pola Arsitektur:** Model-View-Controller (MVC) + Service Layer Pattern
- **Struktur Folder Kunci:**
  - `routes/api.php` : Pintu gerbang seluruh rute API.
  - `app/Http/Controllers/Api/*` : Pengatur alur request HTTP & format response.
  - `app/Services/*` : Logika bisnis mendalam (kalkulasi biaya, upload berkas, transaksi database).
  - `app/Models/*` : Representasi tabel, relasi Eloquent, dan type casting.
  - `app/Http/Requests/*` : Validasi terisolasi turunan `BaseFormRequest`.
  - `app/Http/Resources/*` : Transformasi data keluaran JSON yang seragam.
  - `app/Observers/*` : Audit logging otomatis berbasis event model Eloquent.

> **Catatan Pembicara (Speaker Notes):**  
> *"Untuk adik-adik SMK RPL, penting dipahami bahwa controller tidak boleh terlalu gemuk atau menampung semua logika. Di Astronova, kita memisahkan tugas: validasi diurus oleh Form Request, alur HTTP diatur oleh Controller, dan perhitungan rumit seperti selisih uang muka ditaruh di Service Layer. Ini membuat kode rapi, mudah dibaca, dan gampang diuji."*

---

## Slide 3: Struktur Database & Relasi Model

### Konten Slide:
- **Total Skema:** 28 Tabel Migrasi di MySQL
- **Relasi Inti Antar-Model:**
  - `Departemen` (1) ── (N) `Karyawan` ── (N) `PengajuanPerjalananTugas`
  - `PengajuanPerjalananTugas` (1) ── (N) `DetailPengajuanPerjalanan` (Kategori Biaya)
  - `PengajuanPerjalananTugas` (1) ── (1) `PengajuanPenggantian` (Reimbursement)
  - `PengajuanPerjalananTugas` (1) ── (N) `DokumenPendukung`, `Itinerary`, `Booking`, `UangMuka`, `Settlement`
  - `PengeluaranPerjalanan` (1) ── (N) `BuktiStruk` (Upload Nota/Kwitansi)
  - `RiwayatPersetujuan` : Relasi Polimorfik (`approvable_type`, `approvable_id`) melayani multi-dokumen.
- **Integritas Constraint:**
  - `restrictOnDelete`: Mencegah terhapusnya departemen yang masih memiliki karyawan atau kategori yang sedang dipakai transaksi.
  - `cascadeOnDelete`: Otomatis membersihkan detail dan struk saat pengajuan induk dihapus.

> **Catatan Pembicara (Speaker Notes):**  
> *"Perhatikan hubungan antar-tabel di sini. Database bukan sekadar tempat menampung teks, tapi memiliki aturan integritas. Kita menggunakan foreign key 'restrict' agar data historis perusahaan tidak hilang sengaja maupun tidak sengaja, dan 'cascade' untuk berkas-berkas lampiran. Relasi polimorfik kita gunakan agar satu tabel persetujuan bisa menyetujui dua jenis dokumen yang berbeda tanpa membuat tabel ganda."*

---

## Slide 4: Route, Controller, dan Alur Request

### Konten Slide:
- **Pola Endpoint RESTful:**
  - `GET /api/{resource}` : Menampilkan daftar data (dengan filter & pagination).
  - `POST /api/{resource}` : Menambahkan data baru (Return HTTP 201).
  - `GET /api/{resource}/{id}` : Menampilkan detail spesifik (Return HTTP 200).
  - `PUT /api/{resource}/{id}` : Memperbarui data secara keseluruhan.
  - `PATCH /api/{resource}/{id}/status` : Transisi status alur kerja (workflow).
  - `DELETE /api/{resource}/{id}` : Menghapus data secara aman.
- **Standarisasi Response (`BaseApiController`):**
  - Response selalu memiliki key: `success` (boolean), `message` (string), dan `data` (object/array) atau `errors` (object).
  - Memudahkan integrasi frontend (Flutter/React/Vue/Android) karena format tidak pernah berubah-ubah.

> **Catatan Pembicara (Speaker Notes):**  
> *"Di industri software house, konsistensi API adalah nomor satu. Frontend developer akan senang kalau format respon backend selalu sama. Di project Astronova, semua controller mewarisi BaseApiController, sehingga respon sukses pasti membawa properti success true, dan jika gagal pasti success false dengan kode status HTTP yang standar."*

---

## Slide 5: Implementasi CRUD Dinamis Menggunakan Database

### Konten Slide:
- **Transaksi Database Terproteksi (`DB::transaction`):**
  - Pembuatan pengajuan perjalanan dinas menyimpan data header sekaligus puluhan rincian estimasi biaya secara atomik. Jika salah satu gagal, seluruh operasi di-rollback.
- **Penomoran Dokumen Otomatis & Dinamis:**
  - Format: `TRV/YYYYMM/0001` (Perjalanan) & `RMB/YYYYMM/0001` (Reimbursement).
  - Nomor dihitung otomatis dari urutan data terakhir di database MySQL pada bulan berjalan.
- **Kalkulasi Bisnis Otomatis di Backend:**
  - `Selisih = Total Pengeluaran Riil - Uang Muka`.
  - Sistem otomatis menetapkan status: `kurang_bayar` (perusahaan mengganti uang karyawan), `lebih_bayar` (karyawan mengembalikan sisa uang muka), atau `nihil`.

> **Catatan Pembicara (Speaker Notes):**  
> *"Contoh nyata CRUD dinamis di sini bukan sekadar insert data sederhana. Saat karyawan membuat reimbursement, sistem langsung mengambil uang muka dari database, menjumlahkan semua struk pengeluaran riil, menghitung selisihnya, dan otomatis menentukan apakah ini kurang bayar atau lebih bayar. Semuanya dilakukan otomatis oleh logika backend di dalam DB transaction."*

---

## Slide 6: Proses Validasi & Penanganan Error

### Konten Slide:
- **Validasi Terpusat (`BaseFormRequest`):**
  - Memeriksa kelengkapan input, tipe data, batas karakter, aturan unik, dan keberadaan foreign key (`exists:kategori_pengeluaran,id_kategori`).
  - Gagal validasi otomatis mengembalikan **HTTP 422 Unprocessable Entity** dengan pesan kesalahan spesifik per kolom.
- **Validasi Alur Workflow (State Machine via Enum):**
  - Mencegah perubahan ilegal: Pengajuan berstatus `disetujui` tidak boleh diubah kembali menjadi `draft` atau diedit datanya.
  - Dokumen hanya dapat disetujui/ditolak jika berstatus `menunggu_persetujuan`.
- **Penanganan Error Terstruktur (`bootstrap/app.php`):**
  - `AuthenticationException` -> HTTP 401 Unauthorized JSON.
  - `NotFoundHttpException` -> HTTP 404 Data Tidak Ditemukan JSON.
  - `QueryException` Conflict -> HTTP 409 Conflict JSON.

> **Catatan Pembicara (Speaker Notes):**  
> *"Salah satu kesalahan pemula adalah membiarkan error 500 HTML bocor ke pengguna. Di backend Astronova, semua error ditangkap dan diubah menjadi JSON yang rapi. Selain validasi form, kita menerapkan State Machine memakai PHP Enum, sehingga status dokumen tidak bisa melompat seenaknya—misalnya dari draft langsung loncat disetujui tanpa proses pengajuan."*

---

## Slide 7: Authentication, Authorization, & Keamanan

### Konten Slide:
- **Laravel Sanctum v4.0:**
  - Autentikasi berbasis Bearer Token yang disimpan terenkripsi di tabel `personal_access_tokens`.
  - Token diterbitkan saat login (`POST /api/login`) dan dicabut saat logout (`POST /api/logout`).
- **Pencegahan Akses Ilegal & IDOR:**
  - Approver dokumen diambil otomatis dari identitas token pengguna yang sedang login (`$request->user()`), bukan dari input form sembarangan.
  - Pemberi delegasi otomatis terikat ke user aktif.
- **Perlindungan Data & Password:**
  - Password di-hash menggunakan algoritma modern `Bcrypt`.
  - Kolom sensitif disembunyikan menggunakan atribut `#[Hidden(['password', 'remember_token'])]` pada model.
  - Proteksi Mass Assignment menggunakan daftar `$fillable` yang ketat.

> **Catatan Pembicara (Speaker Notes):**  
> *"Keamanan adalah pilar penting backend. Kita tidak menyimpan token di teks biasa, melainkan memakai Sanctum. Siapa pun yang mencoba menembus endpoint tanpa token langsung ditolak dengan HTTP 401. Selain itu, kita mencegah celah IDOR: seorang staf biasa tidak bisa sembarangan menyetujui dokumen atas nama manajer karena identitas approver divalidasi langsung dari sesi login di server."*

---

## Slide 8: Dari Mock Data Menuju Data Database Dinamis

### Konten Slide:
- **Transformasi Total Backend:**
  - **Sebelumnya:** Migrasi belum dijalankan, tidak ada endpoint agregasi dashboard, controller pengeluaran tidak bisa difilter.
  - **Sekarang:** 
    1. 28 tabel migrasi MySQL terpasang persisten.
    2. Endpoint `/api/dashboard/stats` dan `/api/dashboard/ringkasan` menghitung data secara real-time dari tabel database.
    3. Endpoint `/api/laporan/*` menyediakan filter rentang tanggal, kategori, dan agregat nilai rata-rata dinamis.
    4. Antarmuka web UI dihilangkan sepenuhnya (Pure Headless REST API), root `/` mengembalikan indeks JSON metadata layanan, dan database MySQL dipopulasikan 100% data korporat riil.
- **Zero Mock / Zero Fallback Palsu:**
  - Saat database kosong, API mengembalikan count `0` dan array `[]` (bukan data rekayasa).
  - Saat data baru dimasukkan, angka statistik langsung ter-update otomatis.

> **Catatan Pembicara (Speaker Notes):**  
> *"Ini adalah inti perbaikan kita: kita membongkar seluruh potensi mock data. Dulu tidak ada endpoint statistik, sekarang kita membuat DashboardController dan LaporanController yang menghitung SUM dan COUNT langsung dari database MySQL. Jika ada transaksi baru yang masuk, angka statistik di dashboard detik itu juga berubah secara live. Tidak ada lagi angka statis yang dikarang-karang."*

---

## Slide 9: Hasil Pengujian Aktual (Test Suite)

### Konten Slide:
- **Framework Pengujian:** PHPUnit 12 pada Lingkungan Database `astronova_test`
- **Total Uji Coba:** **99 Test Cases | 525 Assertions**
- **Status Akhir:** **100% PASSED (0 Gagal, 0 Error)**
- **Durasi Eksekusi:** 17,9 detik
- **Cakupan Pengujian:**
  - Autentikasi, Rotasi Token, & Logout (9 Tests)
  - CRUD Master Data & Proteksi Delete Restrict (13 Tests)
  - Siklus Pengajuan Perjalanan & Alur Persetujuan (18 Tests)
  - Kalkulasi Keuangan Reimbursement (Kurang/Lebih Bayar) (6 Tests)
  - Upload Berkas Struk & Dokumen Pendukung ke Storage (20 Tests)
  - Audit Trail Observer Otomatis (7 Tests)
  - Modul Operasional Tambahan (Vendor, Policy, Booking, Uang Muka) (9 Tests)
  - Dashboard & Laporan Dinamis (Database Kosong & Berisi) (9 Tests)

> **Catatan Pembicara (Speaker Notes):**  
> *"Seorang engineer profesional tidak boleh hanya mengklaim kode bekerja tanpa bukti. Di sini kita membuktikannya dengan automated testing: 99 test case dengan 525 assertions seluruhnya LULUS 100%. Kita menguji skenario positif saat data valid, skenario negatif saat validasi gagal, kondisi database kosong, hingga simulasi upload berkas PDF dan JPG asli ke disk storage."*

---

## Slide 10: Kesimpulan & Demonstrasi Fitur

### Konten Slide:
- **Kesimpulan Utama:**
  - Backend PT Astronova kini 100% dinamis, kokoh, dan teruji secara ketat.
  - Memenuhi standar industri software: Clean Code, MVC + Service, DB Transaction, REST API Standard, dan Automated Testing.
  - Siap diintegrasikan dengan aplikasi Frontend (Web / Mobile).
- **Langkah Demonstrasi Langsung:**
  1. Login via Postman atau Live Portal: `POST /api/login`.
  2. Buka Live Portal di browser: `http://localhost:8000`.
  3. Cek statistik dashboard: `GET /api/dashboard/stats`.
  4. Buat pengeluaran baru dan lihat perubahan angka statistik secara instan.
- **Tanya Jawab (Q&A)**

> **Catatan Pembicara (Speaker Notes):**  
> *"Sebagai penutup, project backend Astronova siap menjadi fondasi aplikasi enterprise yang tangguh. Melalui project ini, kita belajar bahwa membangun backend bukan cuma soal membuat route dan controller, tetapi juga menjaga konsistensi arsitektur, keamanan data, dan integritas bisnis. Sekarang, mari kita lihat demonstrasi langsung fitur-fiturnya di layar browser. Terima kasih!"*
