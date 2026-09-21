<?php

namespace Database\Seeders;

use App\Enums\KondisiPenggantianEnum;
use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\DetailPengajuanPenggantian;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Tujuan;
use Illuminate\Database\Seeder;

class PengajuanPenggantianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();
        $kategoriList = KategoriPengeluaran::take(2)->get();

        if (! $karyawan || ! $tujuan) {
            return;
        }

        // Approved Pengajuan Perjalanan 1 (Uang Muka 3,000,000)
        $pengajuan1 = PengajuanPerjalananTugas::firstOrCreate(
            ['nomor_pengajuan' => 'TRV/202609/0099'],
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'id_tujuan' => $tujuan->id_tujuan,
                'tanggal_mulai' => '2026-09-10',
                'tanggal_selesai' => '2026-09-14',
                'maksud_tujuan' => 'Perjalanan dinas disetujui untuk reimburshing test',
                'estimasi_biaya' => 4000000.00,
                'uang_muka' => 3000000.00,
                'status' => StatusPengajuanEnum::DISETUJUI->value,
                'catatan' => 'Telah disetujui manajemen',
            ]
        );

        // Pengajuan Penggantian 1 (Total 4,200,000 > Uang Muka 3,000,000 -> Kurang Bayar 1,200,000)
        $penggantian1 = PengajuanPenggantian::firstOrCreate(
            ['nomor_penggantian' => 'RMB/202609/0001'],
            [
                'id_pengajuan' => $pengajuan1->id_pengajuan,
                'tanggal_pengajuan' => '2026-09-15',
                'total_pengeluaran' => 4200000.00,
                'uang_muka' => 3000000.00,
                'selisih' => 1200000.00,
                'kondisi_penggantian' => KondisiPenggantianEnum::KURANG_BAYAR->value,
                'status' => StatusPenggantianEnum::DRAFT->value,
                'catatan' => 'Pengajuan klaim kekurangan biaya akomodasi',
            ]
        );

        if ($penggantian1->wasRecentlyCreated) {
            if ($kategoriList->count() >= 1) {
                DetailPengajuanPenggantian::create([
                    'id_penggantian' => $penggantian1->id_penggantian,
                    'id_kategori' => $kategoriList[0]->id_kategori,
                    'tanggal_pengeluaran' => '2026-09-10',
                    'deskripsi' => 'Tiket Pesawat & Taxi',
                    'jumlah' => 2500000.00,
                    'bukti_file' => 'struk_tiket_01.pdf',
                ]);
            }

            if ($kategoriList->count() >= 2) {
                DetailPengajuanPenggantian::create([
                    'id_penggantian' => $penggantian1->id_penggantian,
                    'id_kategori' => $kategoriList[1]->id_kategori,
                    'tanggal_pengeluaran' => '2026-09-12',
                    'deskripsi' => 'Hotel 3 Malam',
                    'jumlah' => 1700000.00,
                    'bukti_file' => 'hotel_invoice.pdf',
                ]);
            }
        }
    }
}
