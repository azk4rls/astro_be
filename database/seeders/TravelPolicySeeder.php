<?php

namespace Database\Seeders;

use App\Models\TravelPolicy;
use Illuminate\Database\Seeder;

class TravelPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            ['latarbelakang' => 'Perjalanan Dinas Antar Kota', 'grade' => 'Staff', 'limit_biaya' => 2500000.00, 'kelas' => 'Ekonomi'],
            ['latarbelakang' => 'Perjalanan Dinas Antar Kota', 'grade' => 'Supervisor', 'limit_biaya' => 4000000.00, 'kelas' => 'Ekonomi'],
            ['latarbelakang' => 'Perjalanan Dinas Luar Pulau', 'grade' => 'Manager', 'limit_biaya' => 7500000.00, 'kelas' => 'Bisnis'],
            ['latarbelakang' => 'Perjalanan Dinas Luar Negeri', 'grade' => 'Direktur', 'limit_biaya' => 25000000.00, 'kelas' => 'Bisnis'],
        ];

        foreach ($policies as $policy) {
            TravelPolicy::firstOrCreate(
                ['latarbelakang' => $policy['latarbelakang'], 'grade' => $policy['grade']],
                $policy
            );
        }
    }
}
