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
        Proyek::firstOrCreate(
            ['kode_proyek' => 'PROJ-CBS'],
            [
                'nama_proyek' => 'Core Banking System',
            ]
        );
    }
}
