<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Master Data Seeders
        $this->call([
            DepartemenSeeder::class,
            UserSeeder::class,
            KaryawanSeeder::class,
            ProyekSeeder::class,
            TujuanSeeder::class,
            KategoriPengeluaranSeeder::class,
            PengeluaranPerjalananSeeder::class,
            PengajuanPerjalananSeeder::class,
            PengajuanPenggantianSeeder::class,
            RiwayatPersetujuanSeeder::class,
            // Modul Operasional Tambahan
            VendorSeeder::class,
            TravelPolicySeeder::class,
            NotifikasiSeeder::class,
            DelegasiSeeder::class,
            ItinerarySeeder::class,
            BookingSeeder::class,
            UangMukaSeeder::class,
            SettlementSeeder::class,
        ]);
    }
}
