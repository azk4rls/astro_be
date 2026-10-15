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
        $pengajuan = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();

        if (! $pengajuan) {
            return;
        }

        Settlement::firstOrCreate(
            ['id_pengajuan' => $pengajuan->id_pengajuan],
            [
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'status' => SettlementStatusEnum::DIAJUKAN->value,
                'total_realisasi' => 3450000.00,
            ]
        );
    }
}
