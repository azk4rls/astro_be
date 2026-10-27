<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemen = [
            'Teknologi Informasi',
            'Keuangan & Akuntansi',
            'Operasional Bisnis',
            'Sumber Daya Manusia',
            'Manajemen Proyek',
        ];

        foreach ($departemen as $nama) {
            Departemen::firstOrCreate(['nama_departemen' => $nama]);
        }
    }
}
