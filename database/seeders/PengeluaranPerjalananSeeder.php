<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranPerjalanan;
use Illuminate\Database\Seeder;

class PengeluaranPerjalananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transportasi = KategoriPengeluaran::where('nama_kategori', 'Transportasi')->first();

        if ($transportasi) {
            PengeluaranPerjalanan::firstOrCreate(
                [
                    'deskripsi' => 'Tiket Kereta Eksekutif Jakarta - Surabaya',
                    'jumlah' => 650000.00,
                ],
                [
                    'id_pengajuan' => 1,
                    'id_kategori' => $transportasi->id_kategori,
                    'tanggal_pengeluaran' => now()->format('Y-m-d'),
                    'metode_pembayaran' => 'Corporate Card',
                    'sudah_diganti' => false,
                ]
            );
        }
    }
}
