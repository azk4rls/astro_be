<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use Illuminate\Database\Seeder;

class KategoriPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Transportasi Antar Kota',
                'deskripsi' => 'Biaya tiket pesawat terbang komersial dan tiket kereta api eksekutif dinas.',
            ],
            [
                'nama_kategori' => 'Transportasi Lokal',
                'deskripsi' => 'Biaya taksi resmi, sewa mobil operasional, BBM dinas, tol, dan parkir bandara.',
            ],
            [
                'nama_kategori' => 'Hotel & Akomodasi',
                'deskripsi' => 'Biaya akomodasi penginapan dan hotel dinas sesuai limit grade perjalanan.',
            ],
            [
                'nama_kategori' => 'Konsumsi & Uang Makan',
                'deskripsi' => 'Uang makan harian (per diem) dan biaya konsumsi resmi selama tugas dinas.',
            ],
            [
                'nama_kategori' => 'Fasilitas & Operasional Teknis',
                'deskripsi' => 'Biaya registrasi kegiatan, sertifikasi teknis lapangan, dan operasional darurat.',
            ],
        ];

        foreach ($categories as $category) {
            KategoriPengeluaran::firstOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                ['deskripsi' => $category['deskripsi']]
            );
        }
    }
}
