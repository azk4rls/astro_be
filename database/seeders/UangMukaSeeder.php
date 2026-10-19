<?php

namespace Database\Seeders;

use App\Enums\FundStatusEnum;
use App\Models\PengajuanPerjalananTugas;
use App\Models\UangMuka;
use Illuminate\Database\Seeder;

class UangMukaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengajuan1 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();
        $pengajuan2 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0002')->first();

        $items = [];

        if ($pengajuan1) {
            $items[] = [
                'id_pengajuan' => $pengajuan1->id_pengajuan,
                'diajukan' => 1000000.00,
                'disetujui' => 1000000.00,
                'status' => FundStatusEnum::DICAIRKAN->value,
                'tanggal' => '2026-09-21',
            ];
        }

        if ($pengajuan2) {
            $items[] = [
                'id_pengajuan' => $pengajuan2->id_pengajuan,
                'diajukan' => 2000000.00,
                'disetujui' => 0.00,
                'status' => FundStatusEnum::DIAJUKAN->value,
                'tanggal' => '2026-10-01',
            ];
        }

        foreach ($items as $item) {
            UangMuka::firstOrCreate(
                ['id_pengajuan' => $item['id_pengajuan']],
                $item
            );
        }
    }
}
