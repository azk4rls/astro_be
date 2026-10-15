<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = User::where('email', 'staff@astronova.co.id')->first();
        $manager = User::where('email', 'manager@astronova.co.id')->first();
        $finance = User::where('email', 'finance@astronova.co.id')->first();

        if (! $staff) {
            return;
        }

        $notifications = [
            [
                'id_user' => $staff->id,
                'judul' => 'Pengajuan Disetujui',
                'isi' => 'Pengajuan perjalanan TRV/202609/0001 telah disetujui atasan.',
                'waktu' => Carbon::parse('2026-09-20 09:15:00'),
                'dibaca' => true,
            ],
            [
                'id_user' => $staff->id,
                'judul' => 'Uang Muka Dicairkan',
                'isi' => 'Uang muka sebesar Rp1.000.000 telah dicairkan oleh bagian keuangan.',
                'waktu' => Carbon::parse('2026-09-21 14:30:00'),
                'dibaca' => false,
            ],
            [
                'id_user' => $manager?->id,
                'judul' => 'Menunggu Persetujuan',
                'isi' => 'Terdapat 1 pengajuan perjalanan yang menunggu persetujuan Anda.',
                'waktu' => Carbon::parse('2026-09-22 08:00:00'),
                'dibaca' => false,
            ],
            [
                'id_user' => $finance?->id,
                'judul' => 'Settlement Baru',
                'isi' => 'Settlement perjalanan TRV/202609/0002 menunggu verifikasi.',
                'waktu' => Carbon::parse('2026-09-23 10:45:00'),
                'dibaca' => false,
            ],
        ];

        foreach ($notifications as $notif) {
            if (! $notif['id_user']) {
                continue;
            }

            Notifikasi::firstOrCreate(
                ['id_user' => $notif['id_user'], 'judul' => $notif['judul']],
                $notif
            );
        }
    }
}
