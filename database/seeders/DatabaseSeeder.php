<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Admin / Test User
        User::firstOrCreate(
            ['email' => 'admin@astronova.co.id'],
            [
                'name' => 'Admin Astronova',
                'password' => bcrypt('password123'),
            ]
        );

        // Master Data Seeders
        $this->call([
            DepartemenSeeder::class,
            KaryawanSeeder::class,
            ProyekSeeder::class,
            TujuanSeeder::class,
            KategoriPengeluaranSeeder::class,
            PengeluaranPerjalananSeeder::class,
        ]);
    }
}
