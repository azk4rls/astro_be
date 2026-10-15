<?php

namespace Database\Seeders;

use App\Enums\BookingStatusEnum;
use App\Models\Booking;
use App\Models\PengajuanPerjalananTugas;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
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

        $bookings = [
            [
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'jenis' => 'Tiket Pesawat',
                'detail' => 'Jakarta (CGK) - Surabaya (SUB) PP',
                'nomor_ref' => 'GIA-772341',
                'vendor' => 'Garuda Indonesia',
                'harga' => 1850000.00,
                'status' => BookingStatusEnum::CONFIRMED->value,
            ],
            [
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'jenis' => 'Hotel',
                'detail' => 'Hotel Santika Surabaya - 3 Malam',
                'nomor_ref' => 'HTL-90123',
                'vendor' => 'Hotel Santika',
                'harga' => 1450000.00,
                'status' => BookingStatusEnum::PENDING->value,
            ],
            [
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'jenis' => 'Transportasi',
                'detail' => 'Sewa kendaraan operasional 3 hari',
                'nomor_ref' => 'BB-450021',
                'vendor' => 'Blue Bird Group',
                'harga' => 600000.00,
                'status' => BookingStatusEnum::CONFIRMED->value,
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::firstOrCreate(
                ['id_pengajuan' => $booking['id_pengajuan'], 'nomor_ref' => $booking['nomor_ref']],
                $booking
            );
        }
    }
}
