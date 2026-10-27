<?php

namespace Database\Seeders;

use App\Enums\SettlementStatusEnum;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Settlement;
use Illuminate\Database\Seeder;

class SettlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p1 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();
        $p3 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202610/0003')->first();

        if ($p1) {
            Settlement::updateOrCreate(
                ['id_pengajuan' => $p1->id_pengajuan],
                [
                    'status' => SettlementStatusEnum::DIAJUKAN->value,
                    'total_realisasi' => 4200000.00,
                ]
            );
        }

        if ($p3) {
            Settlement::updateOrCreate(
                ['id_pengajuan' => $p3->id_pengajuan],
                [
                    'status' => SettlementStatusEnum::SELESAI->value,
                    'total_realisasi' => 1500000.00,
                ]
            );
        }
    }
}
