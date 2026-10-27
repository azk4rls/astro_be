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
        $karyawanList = Karyawan::all();
        $proyekList = Proyek::all();
        $tujuanSurabaya = Tujuan::where('nama_tujuan', 'Surabaya')->first() ?? Tujuan::first();
        $tujuanBandung = Tujuan::where('nama_tujuan', 'Bandung')->first() ?? Tujuan::first();
        $tujuanYogya = Tujuan::where('nama_tujuan', 'Yogyakarta')->first() ?? Tujuan::first();
        $tujuanBali = Tujuan::where('nama_tujuan', 'Denpasar')->first() ?? Tujuan::first();
        $tujuanBalikpapan = Tujuan::where('nama_tujuan', 'Balikpapan')->first() ?? Tujuan::first();
        $tujuanMedan = Tujuan::where('nama_tujuan', 'Medan')->first() ?? Tujuan::first();

        $kategoriTransport = KategoriPengeluaran::where('nama_kategori', 'like', '%Transportasi Antar%')->first() ?? KategoriPengeluaran::first();
        $kategoriHotel = KategoriPengeluaran::where('nama_kategori', 'like', '%Hotel%')->first() ?? KategoriPengeluaran::first();
        $kategoriMakan = KategoriPengeluaran::where('nama_kategori', 'like', '%Konsumsi%')->first() ?? KategoriPengeluaran::first();

        if ($karyawanList->isEmpty() || ! $tujuanSurabaya) {
            return;
        }

        $karyawan1 = $karyawanList[0];
        $karyawan2 = $karyawanList[1] ?? $karyawan1;
        $karyawan3 = $karyawanList[2] ?? $karyawan1;
        $karyawan4 = $karyawanList[3] ?? $karyawan1;

        $proyekCBS = $proyekList->where('kode_proyek', 'PROJ-CBS')->first() ?? $proyekList->first();
        $proyekSEC = $proyekList->where('kode_proyek', 'PROJ-SEC')->first() ?? $proyekList->first();
        $proyekERP = $proyekList->where('kode_proyek', 'PROJ-ERP')->first() ?? $proyekList->first();
        $proyekDC = $proyekList->where('kode_proyek', 'PROJ-DC')->first() ?? $proyekList->first();

        // 1. Surabaya - Disetujui
        $p1 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202609/0001'],
            [
                'id_karyawan' => $karyawan1->id_karyawan,
                'id_proyek' => $proyekCBS?->id_proyek,
                'id_tujuan' => $tujuanSurabaya->id_tujuan,
                'tanggal_mulai' => '2026-09-25',
                'tanggal_selesai' => '2026-09-28',
                'maksud_tujuan' => 'Kunjungan kerja teknis implementasi Core Banking System dan integrasi gateway pembayaran di kantor cabang Surabaya',
                'estimasi_biaya' => 3500000.00,
                'uang_muka' => 1000000.00,
                'status' => StatusPengajuanEnum::DISETUJUI->value,
                'catatan' => 'Uang muka telah diverifikasi dan disetujui untuk pencairan kas dinas.',
            ]
        );

        DetailPengajuanPerjalanan::updateOrCreate(
            ['id_pengajuan' => $p1->id_pengajuan, 'keterangan' => 'Tiket pesawat PP Jakarta - Surabaya'],
            [
                'id_kategori' => $kategoriTransport->id_kategori,
                'estimasi_biaya' => 1850000.00,
            ]
        );
        DetailPengajuanPerjalanan::updateOrCreate(
            ['id_pengajuan' => $p1->id_pengajuan, 'keterangan' => 'Akomodasi Hotel Santika Surabaya 3 Malam'],
            [
                'id_kategori' => $kategoriHotel->id_kategori,
                'estimasi_biaya' => 1350000.00,
            ]
        );
        DetailPengajuanPerjalanan::updateOrCreate(
            ['id_pengajuan' => $p1->id_pengajuan, 'keterangan' => 'Uang saku dan konsumsi dinas 4 hari'],
            [
                'id_kategori' => $kategoriMakan->id_kategori,
                'estimasi_biaya' => 300000.00,
            ]
        );

        // 2. Bandung - Menunggu Persetujuan
        $p2 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202610/0002'],
            [
                'id_karyawan' => $karyawan4->id_karyawan,
                'id_proyek' => $proyekSEC?->id_proyek,
                'id_tujuan' => $tujuanBandung->id_tujuan,
                'tanggal_mulai' => '2026-10-15',
                'tanggal_selesai' => '2026-10-17',
                'maksud_tujuan' => 'Audit kepatuhan infrastruktur data center dan asesmen kerentanan perimeter jaringan kantor regional Bandung',
                'estimasi_biaya' => 4800000.00,
                'uang_muka' => 2000000.00,
                'status' => StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value,
                'catatan' => 'Menunggu validasi dokumen surat tugas dari Kepala Divisi Keamanan Informasi.',
            ]
        );

        DetailPengajuanPerjalanan::updateOrCreate(
            ['id_pengajuan' => $p2->id_pengajuan, 'keterangan' => 'Tiket Kereta Cepat Whoosh Eksekutif PP'],
            [
                'id_kategori' => $kategoriTransport->id_kategori,
                'estimasi_biaya' => 1200000.00,
            ]
        );
        DetailPengajuanPerjalanan::updateOrCreate(
            ['id_pengajuan' => $p2->id_pengajuan, 'keterangan' => 'Hotel Ibis Styles Bandung 2 Malam'],
            [
                'id_kategori' => $kategoriHotel->id_kategori,
                'estimasi_biaya' => 1800000.00,
            ]
        );

        // 3. Yogyakarta - Selesai
        $p3 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202610/0003'],
            [
                'id_karyawan' => $karyawan2->id_karyawan,
                'id_proyek' => $proyekERP?->id_proyek,
                'id_tujuan' => $tujuanYogya->id_tujuan,
                'tanggal_mulai' => '2026-10-01',
                'tanggal_selesai' => '2026-10-04',
                'maksud_tujuan' => 'Workshop User Acceptance Testing (UAT) modul supply chain ERP dengan stakeholder regional Yogyakarta',
                'estimasi_biaya' => 3200000.00,
                'uang_muka' => 1500000.00,
                'status' => StatusPengajuanEnum::DISETUJUI->value,
                'catatan' => 'Tugas dinas telah selesai dilaksanakan dan laporan pertanggungjawaban telah disetujui.',
            ]
        );

        // 4. Denpasar - Disetujui
        $p4 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202610/0004'],
            [
                'id_karyawan' => $karyawan3->id_karyawan,
                'id_proyek' => $proyekDC?->id_proyek,
                'id_tujuan' => $tujuanBali->id_tujuan,
                'tanggal_mulai' => '2026-10-20',
                'tanggal_selesai' => '2026-10-24',
                'maksud_tujuan' => 'Implementasi Disaster Recovery Site dan pengujian backup failover infrastruktur cloud di Bali',
                'estimasi_biaya' => 5500000.00,
                'uang_muka' => 2500000.00,
                'status' => StatusPengajuanEnum::DISETUJUI->value,
                'catatan' => 'Tiket dan hotel telah divalidasi oleh Divisi Travel Admin.',
            ]
        );

        // 5. Balikpapan - Draft
        $p5 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202610/0005'],
            [
                'id_karyawan' => $karyawan1->id_karyawan,
                'id_proyek' => $proyekDC?->id_proyek,
                'id_tujuan' => $tujuanBalikpapan->id_tujuan,
                'tanggal_mulai' => '2026-11-05',
                'tanggal_selesai' => '2026-11-08',
                'maksud_tujuan' => 'Survei teknis kesiapan fasilitas colocation dan mitigasi risiko jaringan data center IKN Nusantara',
                'estimasi_biaya' => 6200000.00,
                'uang_muka' => 0.00,
                'status' => StatusPengajuanEnum::DRAFT->value,
                'catatan' => 'Draft pengajuan sedang dalam penyusunan rincian jadwal kegiatan.',
            ]
        );

        // 6. Medan - Ditolak
        $p6 = PengajuanPerjalananTugas::updateOrCreate(
            ['nomor_pengajuan' => 'TRV/202609/0006'],
            [
                'id_karyawan' => $karyawan2->id_karyawan,
                'id_proyek' => $proyekSEC?->id_proyek,
                'id_tujuan' => $tujuanMedan->id_tujuan,
                'tanggal_mulai' => '2026-09-18',
                'tanggal_selesai' => '2026-09-20',
                'maksud_tujuan' => 'Kunjungan koordinasi rutin internal tanpa justifikasi operasional mendesak',
                'estimasi_biaya' => 7500000.00,
                'uang_muka' => 0.00,
                'status' => StatusPengajuanEnum::DITOLAK->value,
                'catatan' => 'Koordinasi dialihkan via video conference daring untuk efisiensi anggaran operasional kuartal III.',
            ]
        );
    }
}
