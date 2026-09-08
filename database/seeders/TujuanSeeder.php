<?php

namespace Database\Seeders;

use App\Models\Tujuan;
use Illuminate\Database\Seeder;

class TujuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tujuan::firstOrCreate(
            ['nama_tujuan' => 'Surabaya'],
            [
                'kota' => 'Surabaya',
                'negara' => 'Indonesia',
            ]
        );
    }
}
