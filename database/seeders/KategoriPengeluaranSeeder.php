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
                'nama_kategori' => 'Transportasi',
                'deskripsi' => 'Biaya tiket pesawat, kereta, taksi, dan transportasi lokal.',
            ],
            [
                'nama_kategori' => 'Hotel',
                'deskripsi' => 'Biaya akomodasi dan penginapan hotel.',
            ],
            [
                'nama_kategori' => 'Konsumsi',
                'deskripsi' => 'Uang makan harian dan biaya konsumsi selama perjalanan dinas.',
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
