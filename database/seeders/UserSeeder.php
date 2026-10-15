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
        $keuangan = Departemen::where('nama_departemen', 'Keuangan')->first();

        $accounts = [
            [
                'email' => 'staff@astronova.co.id',
                'name' => 'Staff Astronova',
                'role' => 'karyawan',
                'jabatan' => 'Software Engineer',
                'nik' => 'USR-001',
                'id_departemen' => $ti?->id_departemen,
            ],
            [
                'email' => 'manager@astronova.co.id',
                'name' => 'Manager Astronova',
                'role' => 'atasan',
                'jabatan' => 'Manager Operasional',
                'nik' => 'USR-002',
                'id_departemen' => $ti?->id_departemen,
            ],
            [
                'email' => 'finance@astronova.co.id',
                'name' => 'Finance Astronova',
                'role' => 'finance',
                'jabatan' => 'Finance Officer',
                'nik' => 'USR-003',
                'id_departemen' => $keuangan?->id_departemen,
            ],
            [
                'email' => 'travel@astronova.co.id',
                'name' => 'Travel Admin Astronova',
                'role' => 'admin-travel',
                'jabatan' => 'Admin Travel',
                'nik' => 'USR-004',
                'id_departemen' => $ti?->id_departemen,
            ],
            [
                'email' => 'admin@astronova.co.id',
                'name' => 'Super Admin Astronova',
                'role' => 'superadmin',
                'jabatan' => 'Super Admin',
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
