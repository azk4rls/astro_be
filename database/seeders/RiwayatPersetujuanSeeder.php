<?php

namespace Database\Seeders;

use App\Enums\ActionPersetujuanEnum;
use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\RiwayatPersetujuan;
use App\Models\User;
use Illuminate\Database\Seeder;

class RiwayatPersetujuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approver = User::where('role', 'atasan')->first() ?? User::query()->orderBy('id')->first();
        $approverId = $approver?->id;
        $approverName = $approver?->name ?? 'Dian Permata';

        // Ambil pengajuan perjalanan yang berstatus menunggu_persetujuan
        $pengajuanPerjalanan = PengajuanPerjalananTugas::where('status', StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value)->first();

        if ($pengajuanPerjalanan) {
            // Approve pengajuan perjalanan
            RiwayatPersetujuan::firstOrCreate(
                [
                    'approvable_type' => 'pengajuan_perjalanan',
                    'approvable_id' => $pengajuanPerjalanan->id_pengajuan,
                    'status' => ActionPersetujuanEnum::DISETUJUI->value,
                ],
                [
                    'id_user' => $approverId,
                    'nama_approver' => $approverName,
                    'peran_approver' => 'Manager',
                    'catatan' => 'Pengajuan perjalanan dinas disetujui oleh manager.',
                    'approved_at' => now(),
                ]
            );

            // Update status dokumen
            $pengajuanPerjalanan->update(['status' => StatusPengajuanEnum::DISETUJUI->value]);
        }

        // Ambil pengajuan penggantian yang berstatus menunggu_persetujuan
        $pengajuanPenggantian = PengajuanPenggantian::where('status', StatusPenggantianEnum::MENUNGGU_PERSETUJUAN->value)->first();

        if ($pengajuanPenggantian) {
            // Reject pengajuan penggantian (sebagai contoh variasi)
            RiwayatPersetujuan::firstOrCreate(
                [
                    'approvable_type' => 'pengajuan_penggantian',
                    'approvable_id' => $pengajuanPenggantian->id_penggantian,
                    'status' => ActionPersetujuanEnum::DITOLAK->value,
                ],
                [
                    'id_user' => $approverId,
                    'nama_approver' => $approverName,
                    'peran_approver' => 'Manager',
                    'catatan' => 'Bukti struk belum lengkap, mohon dilengkapi terlebih dahulu.',
                    'approved_at' => now(),
                ]
            );

            // Update status dokumen
            $pengajuanPenggantian->update(['status' => StatusPenggantianEnum::DITOLAK->value]);
        }
    }
}
