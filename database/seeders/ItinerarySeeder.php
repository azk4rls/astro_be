<?php

namespace Database\Seeders;

use App\Models\Itinerary;
use App\Models\PengajuanPerjalananTugas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengajuan = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();

        if (! $pengajuan) {
            return;
        }

        $items = [
            ['waktu' => '2026-09-25 06:00:00', 'aktivitas' => 'Keberangkatan dari Jakarta', 'lokasi' => 'Bandara Soekarno-Hatta'],
            ['waktu' => '2026-09-25 10:30:00', 'aktivitas' => 'Tiba di Surabaya & Check-in Hotel', 'lokasi' => 'Hotel Santika Surabaya'],
            ['waktu' => '2026-09-26 09:00:00', 'aktivitas' => 'Rapat koordinasi dengan tim cabang', 'lokasi' => 'Kantor Cabang Surabaya'],
            ['waktu' => '2026-09-28 15:00:00', 'aktivitas' => 'Kepulangan ke Jakarta', 'lokasi' => 'Bandara Juanda'],
        ];

        foreach ($items as $item) {
            Itinerary::firstOrCreate(
                ['id_pengajuan' => $pengajuan->id_pengajuan, 'aktivitas' => $item['aktivitas']],
                [
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'waktu' => Carbon::parse($item['waktu']),
                    'aktivitas' => $item['aktivitas'],
                    'lokasi' => $item['lokasi'],
                ]
            );
        }
    }
}
