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
        $keuangan = Departemen::where('nama_departemen', 'like', 'Keuangan%')->first();
        $operasional = Departemen::where('nama_departemen', 'like', 'Operasional%')->first();
        $pmo = Departemen::where('nama_departemen', 'like', 'Manajemen%')->first();

        $karyawanList = [
            [
                'nik' => 'KRY-001',
                'nama_lengkap' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@astronova.co.id',
                'telepon' => '081234567890',
                'id_departemen' => $ti?->id_departemen ?? 1,
                'peran' => 'Cloud Infrastructure Specialist',
            ],
            [
                'nik' => 'KRY-002',
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi.santoso@astronova.co.id',
                'telepon' => '081298765432',
                'id_departemen' => $keuangan?->id_departemen ?? 2,
                'peran' => 'Financial Analyst & Auditor',
            ],
            [
                'nik' => 'KRY-003',
                'nama_lengkap' => 'Maya Lestari',
                'email' => 'finance@astronova.co.id',
                'telepon' => '081122334455',
                'id_departemen' => $keuangan?->id_departemen ?? 2,
                'peran' => 'Senior Finance Officer',
            ],
            [
                'nik' => 'KRY-004',
                'nama_lengkap' => 'Rian Pratama',
                'email' => 'staff@astronova.co.id',
                'telepon' => '081211223344',
                'id_departemen' => $ti?->id_departemen ?? 1,
                'peran' => 'Senior Software Engineer',
            ],
            [
                'nik' => 'KRY-005',
                'nama_lengkap' => 'Dian Permata',
                'email' => 'manager@astronova.co.id',
                'telepon' => '081377889900',
                'id_departemen' => $operasional?->id_departemen ?? $ti?->id_departemen ?? 1,
                'peran' => 'Operations & IT Manager',
            ],
            [
                'nik' => 'KRY-006',
                'nama_lengkap' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@astronova.co.id',
                'telepon' => '081555667788',
                'id_departemen' => $pmo?->id_departemen ?? $ti?->id_departemen ?? 1,
                'peran' => 'Lead Project Manager',
            ],
        ];

        foreach ($karyawanList as $k) {
            Karyawan::updateOrCreate(
                ['email' => $k['email']],
                $k
            );
        }
    }
}
