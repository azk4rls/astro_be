<?php

namespace Tests\Feature;

use App\Models\PengajuanPerjalananTugas;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_dashboard_stats(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_dashboard_stats_on_empty_database_returns_valid_zero_aggregates(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Statistik dashboard berhasil diambil.',
                'data' => [
                    'perjalanan' => [
                        'total' => 0,
                        'draft' => 0,
                        'menunggu_persetujuan' => 0,
                        'disetujui' => 0,
                        'ditolak' => 0,
                        'total_estimasi_biaya' => 0,
                        'total_uang_muka_diajukan' => 0,
                    ],
                    'reimbursement' => [
                        'total' => 0,
                        'total_klaim' => 0,
                        'total_kurang_bayar' => 0,
                        'total_lebih_bayar' => 0,
                    ],
                    'pengeluaran' => [
                        'total_biaya_aktual' => 0,
                        'total_transaksi' => 0,
                    ],
                    'operasional' => [
                        'uang_muka_dicairkan' => 0,
                        'total_booking' => 0,
                        'total_settlement' => 0,
                    ],
                    'kategori_breakdown' => [],
                    'aktivitas_terbaru' => [],
                ],
            ]);
    }

    public function test_dashboard_stats_with_seeded_data_returns_accurate_database_metrics(): void
    {
        $this->seed();
        $user = User::first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'perjalanan' => [
                        'total',
                        'draft',
                        'menunggu_persetujuan',
                        'disetujui',
                        'ditolak',
                        'total_estimasi_biaya',
                        'total_uang_muka_diajukan',
                    ],
                    'reimbursement' => [
                        'total',
                        'total_klaim',
                        'total_kurang_bayar',
                        'total_lebih_bayar',
                    ],
                    'pengeluaran' => [
                        'total_biaya_aktual',
                        'total_transaksi',
                        'sudah_diganti',
                        'belum_diganti',
                    ],
                    'operasional',
                    'master',
                    'kategori_breakdown',
                    'aktivitas_terbaru',
                ],
            ]);

        // Verifikasi kesesuaian nilai aktual dengan query database
        $actualPerjalananCount = PengajuanPerjalananTugas::count();
        $this->assertEquals($actualPerjalananCount, $response->json('data.perjalanan.total'));

        $actualPengeluaranSum = (float) PengeluaranPerjalanan::sum('jumlah');
        $this->assertEquals($actualPengeluaranSum, $response->json('data.pengeluaran.total_biaya_aktual'));
    }

    public function test_dashboard_ringkasan_returns_valid_kpi(): void
    {
        $this->seed();
        $user = User::first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/ringkasan');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ringkasan KPI berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_perjalanan',
                    'persetujuan_tertunda',
                    'total_pengeluaran_riil',
                    'total_reimbursement_disetujui',
                ],
            ]);
    }
}
