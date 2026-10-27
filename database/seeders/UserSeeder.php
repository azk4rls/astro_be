<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed one login account per role.
     */
    public function run(): void
    {
        $ti = Departemen::where('nama_departemen', 'Teknologi Informasi')->first();
        $keuangan = Departemen::where('nama_departemen', 'like', 'Keuangan%')->first();
        $operasional = Departemen::where('nama_departemen', 'like', 'Operasional%')->first();

        $accounts = [
            [
                'email' => 'staff@astronova.co.id',
                'name' => 'Rian Pratama',
                'role' => 'karyawan',
                'jabatan' => 'Senior Software Engineer',
                'nik' => 'USR-001',
                'id_departemen' => $ti?->id_departemen,
            ],
            [
                'email' => 'manager@astronova.co.id',
                'name' => 'Dian Permata',
                'role' => 'atasan',
                'jabatan' => 'Operations & IT Manager',
                'nik' => 'USR-002',
                'id_departemen' => $operasional?->id_departemen ?? $ti?->id_departemen,
            ],
            [
                'email' => 'finance@astronova.co.id',
                'name' => 'Maya Lestari',
                'role' => 'finance',
                'jabatan' => 'Senior Finance Officer',
                'nik' => 'USR-003',
                'id_departemen' => $keuangan?->id_departemen,
            ],
            [
                'email' => 'travel@astronova.co.id',
                'name' => 'Bambang Sudiro',
                'role' => 'admin-travel',
                'jabatan' => 'Corporate Travel Administrator',
                'nik' => 'USR-004',
                'id_departemen' => $operasional?->id_departemen ?? $ti?->id_departemen,
            ],
            [
                'email' => 'admin@astronova.co.id',
                'name' => 'Super Administrator',
                'role' => 'superadmin',
                'jabatan' => 'Head of Technology & Systems',
                'nik' => 'USR-005',
                'id_departemen' => $ti?->id_departemen,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['password' => bcrypt('password123')]
            );
        }
    }
}
