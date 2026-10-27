<?php

namespace Tests\Feature;

use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_cannot_access_laporan_endpoints(): void
    {
        $this->getJson('/api/laporan/pengeluaran')->assertStatus(401);
        $this->getJson('/api/laporan/perjalanan')->assertStatus(401);
        $this->getJson('/api/laporan/reimbursement')->assertStatus(401);
    }

    public function test_laporan_pengeluaran_empty_returns_zero_metrics(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/laporan/pengeluaran');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'ringkasan' => [
                        'total_biaya' => 0,
                        'total_transaksi' => 0,
                        'rata_rata_per_transaksi' => 0,
                    ],
                ],
            ]);
    }

    public function test_laporan_pengeluaran_with_filters_and_aggregates(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $kategoriA = KategoriPengeluaran::create(['nama_kategori' => 'Transport Laporan']);
        $kategoriB = KategoriPengeluaran::create(['nama_kategori' => 'Konsumsi Laporan']);

        PengeluaranPerjalanan::create([
            'id_kategori' => $kategoriA->id_kategori,
            'tanggal_pengeluaran' => '2026-10-01',
            'deskripsi' => 'Tiket KA Argo',
            'jumlah' => 400000,
            'metode_pembayaran' => 'Transfer',
            'sudah_diganti' => true,
        ]);

        PengeluaranPerjalanan::create([
            'id_kategori' => $kategoriA->id_kategori,
            'tanggal_pengeluaran' => '2026-10-05',
            'deskripsi' => 'Taksi bandara',
            'jumlah' => 200000,
            'metode_pembayaran' => 'Cash',
            'sudah_diganti' => false,
        ]);

        PengeluaranPerjalanan::create([
            'id_kategori' => $kategoriB->id_kategori,
            'tanggal_pengeluaran' => '2026-10-02',
            'deskripsi' => 'Makan malam',
            'jumlah' => 150000,
            'metode_pembayaran' => 'Cash',
            'sudah_diganti' => true,
        ]);

        // Filter by kategoriA
        $response = $this->getJson("/api/laporan/pengeluaran?id_kategori={$kategoriA->id_kategori}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'ringkasan' => [
                        'total_biaya' => 600000,
                        'total_transaksi' => 2,
                        'rata_rata_per_transaksi' => 300000,
                    ],
                ],
            ]);

        // Filter by date range
        $responseDate = $this->getJson('/api/laporan/pengeluaran?start_date=2026-10-03&end_date=2026-10-06');
        $responseDate->assertStatus(200)
            ->assertJsonPath('data.ringkasan.total_transaksi', 1)
            ->assertJsonPath('data.ringkasan.total_biaya', 200000);
    }

    public function test_laporan_perjalanan_with_filters(): void
    {
        $this->seed();
        $user = User::first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/laporan/perjalanan');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'ringkasan' => [
                        'total_perjalanan',
                        'total_estimasi_biaya',
                        'total_uang_muka',
                        'rata_rata_estimasi',
                    ],
                    'data',
                ],
            ]);

        $this->assertEquals(
            PengajuanPerjalananTugas::count(),
            $response->json('data.ringkasan.total_perjalanan')
        );
    }

    public function test_laporan_reimbursement_with_filters(): void
    {
        $this->seed();
        $user = User::first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/laporan/reimbursement');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'ringkasan' => [
                        'total_klaim',
                        'total_pengeluaran',
                        'total_uang_muka',
                        'total_kurang_bayar',
                        'total_lebih_bayar',
                    ],
                    'data',
                ],
            ]);

        $this->assertEquals(
            PengajuanPenggantian::count(),
            $response->json('data.ringkasan.total_klaim')
        );
    }
}
