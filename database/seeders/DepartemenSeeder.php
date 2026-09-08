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
        Departemen::firstOrCreate(
            ['nama_departemen' => 'Teknologi Informasi']
        );

        Departemen::firstOrCreate(
            ['nama_departemen' => 'Keuangan']
        );
    }
}
