<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            ['nama_vendor' => 'Garuda Indonesia', 'kategori' => 'Maskapai', 'kontak' => '021-25601111', 'rating' => 4.7],
            ['nama_vendor' => 'Citilink', 'kategori' => 'Maskapai', 'kontak' => '021-80820000', 'rating' => 4.2],
            ['nama_vendor' => 'Kereta Api Indonesia', 'kategori' => 'Kereta', 'kontak' => '121', 'rating' => 4.5],
            ['nama_vendor' => 'Hotel Santika', 'kategori' => 'Hotel', 'kontak' => '021-29507888', 'rating' => 4.4],
            ['nama_vendor' => 'Aston Inn', 'kategori' => 'Hotel', 'kontak' => '021-88888888', 'rating' => 4.1],
            ['nama_vendor' => 'Traveloka', 'kategori' => 'OTA', 'kontak' => '0804-1-500-500', 'rating' => 4.6],
            ['nama_vendor' => 'Blue Bird Group', 'kategori' => 'Transportasi', 'kontak' => '021-79171234', 'rating' => 4.3],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(['nama_vendor' => $vendor['nama_vendor']], $vendor);
        }
    }
}
