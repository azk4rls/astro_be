<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPerjalananTugas;
use App\Models\PengeluaranPerjalanan;
use Illuminate\Database\Seeder;

class PengeluaranPerjalananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p1 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202609/0001')->first();
        $p3 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202610/0003')->first();
        $p4 = PengajuanPerjalananTugas::where('nomor_pengajuan', 'TRV/202610/0004')->first();

        $kategoriTransport = KategoriPengeluaran::where('nama_kategori', 'like', '%Antar Kota%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriLokal = KategoriPengeluaran::where('nama_kategori', 'like', '%Lokal%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriHotel = KategoriPengeluaran::where('nama_kategori', 'like', '%Hotel%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriKonsumsi = KategoriPengeluaran::where('nama_kategori', 'like', '%Konsumsi%')->first()
            ?? KategoriPengeluaran::first();
        $kategoriTeknis = KategoriPengeluaran::where('nama_kategori', 'like', '%Operasional Teknis%')->first()
            ?? KategoriPengeluaran::first();

        $expenses = [
            // Pengeluaran untuk TRV/202609/0001 (Surabaya)
            [
                'id_pengajuan' => $p1?->id_pengajuan,
                'id_kategori' => $kategoriTransport->id_kategori,
                'tanggal_pengeluaran' => '2026-09-25',
                'deskripsi' => 'Tiket Pesawat Garuda Indonesia GA-310 Jakarta (CGK) - Surabaya (SUB) PP',
                'jumlah' => 1850000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => true,
            ],
            [
                'id_pengajuan' => $p1?->id_pengajuan,
                'id_kategori' => $kategoriHotel->id_kategori,
                'tanggal_pengeluaran' => '2026-09-28',
                'deskripsi' => 'Akomodasi Hotel Santika Premiere Gubeng Surabaya (3 Malam)',
                'jumlah' => 1350000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => true,
            ],
            [
                'id_pengajuan' => $p1?->id_pengajuan,
                'id_kategori' => $kategoriLokal->id_kategori,
                'tanggal_pengeluaran' => '2026-09-26',
                'deskripsi' => 'Taksi Blue Bird Bandara Juanda ke Hotel & Operasional Kantor Cabang',
                'jumlah' => 450000.00,
                'metode_pembayaran' => 'Reimbursement',
                'sudah_diganti' => true,
            ],
            [
                'id_pengajuan' => $p1?->id_pengajuan,
                'id_kategori' => $kategoriKonsumsi->id_kategori,
                'tanggal_pengeluaran' => '2026-09-28',
                'deskripsi' => 'Uang makan harian dan konsumsi tim teknis selama 4 hari dinas',
                'jumlah' => 550000.00,
                'metode_pembayaran' => 'Reimbursement',
                'sudah_diganti' => true,
            ],

            // Pengeluaran untuk TRV/202610/0003 (Yogyakarta)
            [
                'id_pengajuan' => $p3?->id_pengajuan,
                'id_kategori' => $kategoriTransport->id_kategori,
                'tanggal_pengeluaran' => '2026-10-01',
                'deskripsi' => 'Tiket Kereta Eksekutif Taksaka Gambir - Tugu Yogyakarta PP',
                'jumlah' => 950000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => true,
            ],
            [
                'id_pengajuan' => $p3?->id_pengajuan,
                'id_kategori' => $kategoriHotel->id_kategori,
                'tanggal_pengeluaran' => '2026-10-04',
                'deskripsi' => 'Hotel Grand Mercure Yogyakarta (3 Malam)',
                'jumlah' => 1200000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => true,
            ],
            [
                'id_pengajuan' => $p3?->id_pengajuan,
                'id_kategori' => $kategoriLokal->id_kategori,
                'tanggal_pengeluaran' => '2026-10-02',
                'deskripsi' => 'Sewa mobil dinas operasional kunjungan kantor cabang Yogyakarta',
                'jumlah' => 600000.00,
                'metode_pembayaran' => 'Reimbursement',
                'sudah_diganti' => true,
            ],

            // Pengeluaran untuk TRV/202610/0004 (Denpasar)
            [
                'id_pengajuan' => $p4?->id_pengajuan,
                'id_kategori' => $kategoriTransport->id_kategori,
                'tanggal_pengeluaran' => '2026-10-20',
                'deskripsi' => 'Tiket Pesawat Citilink Jakarta (CGK) - Denpasar (DPS) PP',
                'jumlah' => 2100000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => false,
            ],
            [
                'id_pengajuan' => $p4?->id_pengajuan,
                'id_kategori' => $kategoriHotel->id_kategori,
                'tanggal_pengeluaran' => '2026-10-24',
                'deskripsi' => 'Hotel Aston Denpasar & Convention Center (4 Malam)',
                'jumlah' => 1650000.00,
                'metode_pembayaran' => 'Corporate Card',
                'sudah_diganti' => false,
            ],
            [
                'id_pengajuan' => $p4?->id_pengajuan,
                'id_kategori' => $kategoriTeknis->id_kategori,
                'tanggal_pengeluaran' => '2026-10-22',
                'deskripsi' => 'Biaya pengujian kabel fiber optik site recovery dan pass card data center',
                'jumlah' => 850000.00,
                'metode_pembayaran' => 'Reimbursement',
                'sudah_diganti' => false,
            ],
        ];

        foreach ($expenses as $expense) {
            PengeluaranPerjalanan::updateOrCreate(
                [
                    'deskripsi' => $expense['deskripsi'],
                    'jumlah' => $expense['jumlah'],
                ],
                $expense
            );
        }
    }
}
