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
        $destinations = [
            ['nama_tujuan' => 'Surabaya', 'kota' => 'Surabaya', 'negara' => 'Indonesia'],
            ['nama_tujuan' => 'Bandung', 'kota' => 'Bandung', 'negara' => 'Indonesia'],
            ['nama_tujuan' => 'Denpasar', 'kota' => 'Denpasar', 'negara' => 'Indonesia'],
            ['nama_tujuan' => 'Yogyakarta', 'kota' => 'Yogyakarta', 'negara' => 'Indonesia'],
            ['nama_tujuan' => 'Balikpapan', 'kota' => 'Balikpapan', 'negara' => 'Indonesia'],
            ['nama_tujuan' => 'Medan', 'kota' => 'Medan', 'negara' => 'Indonesia'],
        ];

        foreach ($destinations as $d) {
            Tujuan::firstOrCreate(
                ['nama_tujuan' => $d['nama_tujuan']],
                [
                    'kota' => $d['kota'],
                    'negara' => $d['negara'],
                ]
            );
        }
    }
}
