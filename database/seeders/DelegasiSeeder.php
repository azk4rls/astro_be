<?php

namespace Database\Seeders;

use App\Models\Delegasi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DelegasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'manager@astronova.co.id')->first();
        $travel = User::where('email', 'travel@astronova.co.id')->first();

        if (! $manager || ! $travel) {
            return;
        }

        $delegasi = [
            [
                'id_pemberi' => $manager->id,
                'id_penerima' => $travel->id,
                'dari_tgl' => '2026-10-01',
                'sampai_tgl' => '2026-10-15',
                'alasan' => 'Cuti tahunan, wewenang persetujuan dialihkan sementara.',
                'aktif' => true,
            ],
        ];

        foreach ($delegasi as $item) {
            Delegasi::firstOrCreate(
                ['id_pemberi' => $item['id_pemberi'], 'id_penerima' => $item['id_penerima'], 'dari_tgl' => $item['dari_tgl']],
                $item
            );
        }
    }
}
