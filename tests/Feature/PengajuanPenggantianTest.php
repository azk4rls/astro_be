<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Tujuan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanPenggantianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_list_pengajuan_penggantian(): void
    {
        $response = $this->getJson('/api/pengajuan-penggantian');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data pengajuan penggantian berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id_penggantian',
                            'nomor_penggantian',
                            'id_pengajuan',
                            'total_pengeluaran',
                            'uang_muka',
                            'selisih',
                            'kondisi_penggantian',
                            'kondisi_penggantian_label',
                            'status',
                            'status_label',
                            'details',
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_create_pengajuan_penggantian_with_kurang_bayar_calculation(): void
    {
        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();
        $kategori = KategoriPengeluaran::first();

        // Create approved pengajuan perjalanan with Uang Muka = 1,000,000
        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/TEST/0001',
            'id_karyawan' => $karyawan->id_karyawan,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-04',
            'maksud_tujuan' => 'Test Pengajuan Reimbursement',
            'estimasi_biaya' => 2000000.00,
            'uang_muka' => 1000000.00,
            'status' => StatusPengajuanEnum::DISETUJUI->value,
        ]);

        // Submit reimbursement with total pengeluaran = 1,500,000 (Kurang Bayar = 500,000)
        $payload = [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'tanggal_pengajuan' => '2026-10-05',
            'catatan' => 'Klaim biaya riil',
            'details' => [
                [
                    'id_kategori' => $kategori->id_kategori,
                    'tanggal_pengeluaran' => '2026-10-02',
                    'deskripsi' => 'Pengeluaran bensin & tol',
                    'jumlah' => 1500000.00,
                    'bukti_file' => 'struk_bensin.jpg',
                ],
            ],
        ];

        $response = $this->postJson('/api/pengajuan-penggantian', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_pengeluaran' => 1500000.00,
                    'uang_muka' => 1000000.00,
                    'selisih' => 500000.00,
                    'kondisi_penggantian' => 'kurang_bayar',
                    'status' => 'draft',
                ],
            ]);

        $this->assertDatabaseHas('pengajuan_penggantian', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'kondisi_penggantian' => 'kurang_bayar',
            'selisih' => 500000.00,
        ]);
    }

    public function test_can_calculate_lebih_bayar_when_total_less_than_uang_muka(): void
    {
        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();
        $kategori = KategoriPengeluaran::first();

        // Create approved pengajuan perjalanan with Uang Muka = 2,000,000
        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/TEST/0002',
            'id_karyawan' => $karyawan->id_karyawan,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-04',
            'maksud_tujuan' => 'Test Pengajuan Lebih Bayar',
            'estimasi_biaya' => 2000000.00,
            'uang_muka' => 2000000.00,
            'status' => StatusPengajuanEnum::DISETUJUI->value,
        ]);

        // Submit reimbursement with total pengeluaran = 1,200,000 (Lebih Bayar = -800,000)
        $payload = [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'tanggal_pengajuan' => '2026-10-05',
            'details' => [
                [
                    'id_kategori' => $kategori->id_kategori,
                    'tanggal_pengeluaran' => '2026-10-02',
                    'jumlah' => 1200000.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/pengajuan-penggantian', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_pengeluaran' => 1200000.00,
                    'uang_muka' => 2000000.00,
                    'selisih' => -800000.00,
                    'kondisi_penggantian' => 'lebih_bayar',
                ],
            ]);
    }

    public function test_cannot_create_reimbursement_for_unapproved_pengajuan(): void
    {
        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();

        // Create DRAFT pengajuan
        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/TEST/DRAFT',
            'id_karyawan' => $karyawan->id_karyawan,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-04',
            'maksud_tujuan' => 'Test Draft',
            'status' => StatusPengajuanEnum::DRAFT->value,
        ]);

        $payload = [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'tanggal_pengajuan' => '2026-10-05',
            'details' => [
                [
                    'id_kategori' => 1,
                    'tanggal_pengeluaran' => '2026-10-02',
                    'jumlah' => 500000.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/pengajuan-penggantian', $payload);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Pengajuan penggantian hanya dapat dibuat untuk pengajuan perjalanan tugas yang telah disetujui.',
            ]);
    }

    public function test_cannot_create_duplicate_reimbursement_for_same_pengajuan(): void
    {
        $existing = PengajuanPenggantian::first();

        $payload = [
            'id_pengajuan' => $existing->id_pengajuan,
            'tanggal_pengajuan' => '2026-10-05',
            'details' => [
                [
                    'id_kategori' => 1,
                    'tanggal_pengeluaran' => '2026-10-02',
                    'jumlah' => 500000.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/pengajuan-penggantian', $payload);

        // Validation rule unique:pengajuan_penggantian,id_pengajuan returns 422
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_update_status_workflow(): void
    {
        $penggantian = PengajuanPenggantian::first();

        // 1. draft -> menunggu_persetujuan
        $response = $this->patchJson("/api/pengajuan-penggantian/{$penggantian->id_penggantian}/status", [
            'status' => StatusPenggantianEnum::MENUNGGU_PERSETUJUAN->value,
            'catatan' => 'Diajukan ke bagian keuangan',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'menunggu_persetujuan',
                ],
            ]);

        // 2. menunggu_persetujuan -> disetujui
        $response2 = $this->patchJson("/api/pengajuan-penggantian/{$penggantian->id_penggantian}/status", [
            'status' => StatusPenggantianEnum::DISETUJUI->value,
            'catatan' => 'Disetujui oleh finance',
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'disetujui',
                ],
            ]);
    }
}
