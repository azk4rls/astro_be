<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ti = Departemen::where('nama_departemen', 'Teknologi Informasi')->first();
        $keuangan = Departemen::where('nama_departemen', 'Keuangan')->first();

        if ($ti) {
            Karyawan::firstOrCreate(
                ['nik' => 'KRY-001'],
                [
                    'nama_lengkap' => 'Ahmad Fauzi',
                    'email' => 'ahmad.fauzi@astronova.co.id',
                    'telepon' => '081234567890',
                    'id_departemen' => $ti->id_departemen,
                    'peran' => 'Software Engineer',
                ]
            );
        }

        if ($keuangan) {
            Karyawan::firstOrCreate(
                ['nik' => 'KRY-002'],
                [
                    'nama_lengkap' => 'Budi Santoso',
                    'email' => 'budi.santoso@astronova.co.id',
                    'telepon' => '081298765432',
                    'id_departemen' => $keuangan->id_departemen,
                    'peran' => 'Financial Analyst',
                ]
            );
        }
    }
}
