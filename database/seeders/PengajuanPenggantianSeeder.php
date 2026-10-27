<?php

namespace Database\Seeders;

use App\Enums\KondisiPenggantianEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\DetailPengajuanPenggantian;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use Illuminate\Database\Seeder;

class PengajuanPenggantianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p1 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();
        $p3 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202610/0003')->first();

        $kategoriTransport = KategoriPengeluaran::where('nama_kategori', 'like', '%Antar Kota%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriHotel = KategoriPengeluaran::where('nama_kategori', 'like', '%Hotel%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriKonsumsi = KategoriPengeluaran::where('nama_kategori', 'like', '%Konsumsi%')->first()
            ?? KategoriPengeluaran::first();

        if (! $p1 || ! $p3) {
            return;
        }

        // 1. Klaim Kurang Bayar untuk Perjalanan Surabaya (Total Realisasi 4.200.000 vs Uang Muka 1.000.000)
        $penggantian1 = PengajuanPenggantian::updateOrCreate(
            ['nomor_penggantian' => 'RMB/202609/0001'],
            [
                'id_pengajuan' => $p1->id_pengajuan,
                'tanggal_pengajuan' => '2026-09-30',
                'total_pengeluaran' => 4200000.00,
                'uang_muka' => 1000000.00,
                'selisih' => 3200000.00,
                'kondisi_penggantian' => KondisiPenggantianEnum::KURANG_BAYAR->value,
                'status' => StatusPenggantianEnum::DRAFT->value,
                'catatan' => 'Draft pengajuan klaim reimbursement atas selisih biaya tiket dan akomodasi dinas Surabaya.',
            ]
        );

        DetailPengajuanPenggantian::updateOrCreate(
            ['id_penggantian' => $penggantian1->id_penggantian, 'deskripsi' => 'Tiket Pesawat Garuda Indonesia GA-310'],
            [
                'id_kategori' => $kategoriTransport->id_kategori,
                'tanggal_pengeluaran' => '2026-09-25',
                'jumlah' => 1850000.00,
                'bukti_file' => 'struk_tiket_garuda_ga310.pdf',
            ]
        );

        DetailPengajuanPenggantian::updateOrCreate(
            ['id_penggantian' => $penggantian1->id_penggantian, 'deskripsi' => 'Invoice Hotel Santika Premiere Gubeng (3 Malam)'],
            [
                'id_kategori' => $kategoriHotel->id_kategori,
                'tanggal_pengeluaran' => '2026-09-28',
                'jumlah' => 1350000.00,
                'bukti_file' => 'invoice_santika_gubeng_surabaya.pdf',
            ]
        );

        DetailPengajuanPenggantian::updateOrCreate(
            ['id_penggantian' => $penggantian1->id_penggantian, 'deskripsi' => 'Taksi dan Konsumsi Harian Dinas'],
            [
                'id_kategori' => $kategoriKonsumsi->id_kategori,
                'tanggal_pengeluaran' => '2026-09-28',
                'jumlah' => 1000000.00,
                'bukti_file' => 'rekap_struk_taksi_konsumsi.pdf',
            ]
        );

        // 2. Klaim Sesuai / Nihil untuk Perjalanan Yogyakarta (Total Realisasi 1.500.000 vs Uang Muka 1.500.000)
        $penggantian2 = PengajuanPenggantian::updateOrCreate(
            ['nomor_penggantian' => 'RMB/202610/0002'],
            [
                'id_pengajuan' => $p3->id_pengajuan,
                'tanggal_pengajuan' => '2026-10-06',
                'total_pengeluaran' => 1500000.00,
                'uang_muka' => 1500000.00,
                'selisih' => 0.00,
                'kondisi_penggantian' => KondisiPenggantianEnum::SESUAI->value,
                'status' => StatusPenggantianEnum::DISETUJUI->value,
                'catatan' => 'Seluruh pengeluaran dinas Yogyakarta sesuai tepat dengan nilai uang muka yang diterima.',
            ]
        );

        DetailPengajuanPenggantian::updateOrCreate(
            ['id_penggantian' => $penggantian2->id_penggantian, 'deskripsi' => 'Tiket Kereta Eksekutif Taksaka PP'],
            [
                'id_kategori' => $kategoriTransport->id_kategori,
                'tanggal_pengeluaran' => '2026-10-01',
                'jumlah' => 950000.00,
                'bukti_file' => 'tiket_kereta_taksaka.pdf',
            ]
        );

        DetailPengajuanPenggantian::updateOrCreate(
            ['id_penggantian' => $penggantian2->id_penggantian, 'deskripsi' => 'Konsumsi dan Transport Lokal Yogyakarta'],
            [
                'id_kategori' => $kategoriKonsumsi->id_kategori,
                'tanggal_pengeluaran' => '2026-10-04',
                'jumlah' => 550000.00,
                'bukti_file' => 'struk_konsumsi_yogya.pdf',
            ]
        );
    }
}
