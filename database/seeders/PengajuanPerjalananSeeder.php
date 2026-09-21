<?php

namespace Database\Seeders;

use App\Enums\StatusPengajuanEnum;
use App\Models\DetailPengajuanPerjalanan;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Proyek;
use App\Models\Tujuan;
use Illuminate\Database\Seeder;

class PengajuanPerjalananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawan = Karyawan::first();
        $proyek = Proyek::first();
        $tujuan = Tujuan::first();
        $kategoriList = KategoriPengeluaran::take(2)->get();

        if (! $karyawan || ! $tujuan) {
            return;
        }

        $pengajuan1 = PengajuanPerjalananTugas::firstOrCreate(
            ['nomor_pengajuan' => 'TRV/202609/0001'],
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'id_proyek' => $proyek?->id_proyek,
                'id_tujuan' => $tujuan->id_tujuan,
                'tanggal_mulai' => '2026-09-25',
                'tanggal_selesai' => '2026-09-28',
                'maksud_tujuan' => 'Kunjungan kerja dan koordinasi teknis tim di cabang Surabaya',
                'estimasi_biaya' => 3500000.00,
                'uang_muka' => 1000000.00,
                'status' => StatusPengajuanEnum::DRAFT->value,
                'catatan' => 'Membutuhkan uang muka operasional awal',
            ]
        );

        if ($pengajuan1->wasRecentlyCreated) {
            if ($kategoriList->count() >= 1) {
                DetailPengajuanPerjalanan::create([
                    'id_pengajuan' => $pengajuan1->id_pengajuan,
                    'id_kategori' => $kategoriList[0]->id_kategori,
                    'estimasi_biaya' => 2000000.00,
                    'keterangan' => 'Tiket pesawat PP Jakarta - Surabaya',
                ]);
            }

            if ($kategoriList->count() >= 2) {
                DetailPengajuanPerjalanan::create([
                    'id_pengajuan' => $pengajuan1->id_pengajuan,
                    'id_kategori' => $kategoriList[1]->id_kategori,
                    'estimasi_biaya' => 1500000.00,
                    'keterangan' => 'Akomodasi Hotel 3 Malam',
                ]);
            }
        }

        $pengajuan2 = PengajuanPerjalananTugas::firstOrCreate(
            ['nomor_pengajuan' => 'TRV/202609/0002'],
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'id_proyek' => $proyek?->id_proyek,
                'id_tujuan' => $tujuan->id_tujuan,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-05',
                'maksud_tujuan' => 'Audit infrastruktur jaringan kantor cabang',
                'estimasi_biaya' => 5000000.00,
                'uang_muka' => 2000000.00,
                'status' => StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value,
                'catatan' => 'Menunggu persetujuan manajer divisi',
            ]
        );

        if ($pengajuan2->wasRecentlyCreated && $kategoriList->count() >= 1) {
            DetailPengajuanPerjalanan::create([
                'id_pengajuan' => $pengajuan2->id_pengajuan,
                'id_kategori' => $kategoriList[0]->id_kategori,
                'estimasi_biaya' => 5000000.00,
                'keterangan' => 'Sewa kendaraan operasional dan tiket penerbangan',
            ]);
        }
    }
}
