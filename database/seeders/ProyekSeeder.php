<?php

namespace Database\Seeders;

use App\Models\Proyek;
use Illuminate\Database\Seeder;

class ProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proyekList = [
            [
                'kode_proyek' => 'PROJ-CBS',
                'nama_proyek' => 'Implementasi Modernisasi Core Banking System',
            ],
            [
                'kode_proyek' => 'PROJ-SEC',
                'nama_proyek' => 'Audit Kepatuhan Keamanan Informasi & ISO 27001',
            ],
            [
                'kode_proyek' => 'PROJ-ERP',
                'nama_proyek' => 'Rollout Sistem Enterprise Resource Planning (ERP)',
            ],
            [
                'kode_proyek' => 'PROJ-DC',
                'nama_proyek' => 'Migrasi Infrastruktur & Disaster Recovery Data Center',
            ],
        ];

        foreach ($proyekList as $p) {
            Proyek::firstOrCreate(
                ['kode_proyek' => $p['kode_proyek']],
                ['nama_proyek' => $p['nama_proyek']]
            );
        }
    }
}
