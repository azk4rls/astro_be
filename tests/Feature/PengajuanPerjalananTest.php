<?php

namespace Tests\Feature;

use App\Enums\StatusPengajuanEnum;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Proyek;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PengajuanPerjalananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_pengajuan_perjalanan(): void
    {
        $response = $this->getJson('/api/pengajuan-perjalanan');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data pengajuan perjalanan berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id_pengajuan',
                            'nomor_pengajuan',
                            'id_karyawan',
                            'status',
                            'status_label',
                            'details',
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_create_pengajuan_perjalanan_with_details(): void
    {
        $karyawan = Karyawan::first();
        $proyek = Proyek::first();
        $tujuan = Tujuan::first();
        $kategori = KategoriPengeluaran::first();

        $payload = [
            'id_karyawan' => $karyawan->id_karyawan,
            'id_proyek' => $proyek->id_proyek,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => '2026-10-10',
            'tanggal_selesai' => '2026-10-15',
            'maksud_tujuan' => 'Testing pembuatan pengajuan perjalanan',
            'uang_muka' => 500000.00,
            'catatan' => 'Uang muka operasional',
            'details' => [
                [
                    'id_kategori' => $kategori->id_kategori,
                    'estimasi_biaya' => 1200000.00,
                    'keterangan' => 'Bensin dan Tol',
                ],
            ],
        ];

        $response = $this->postJson('/api/pengajuan-perjalanan', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pengajuan perjalanan tugas berhasil dibuat.',
                'data' => [
                    'status' => 'draft',
                    'estimasi_biaya' => 1200000.00,
                ],
            ]);

        $this->assertDatabaseHas('pengajuan_perjalanan_tugas', [
            'maksud_tujuan' => 'Testing pembuatan pengajuan perjalanan',
            'status' => 'draft',
        ]);
    }

    public function test_can_show_pengajuan_perjalanan_detail(): void
    {
        $pengajuan = PengajuanPerjalananTugas::first();

        $response = $this->getJson("/api/pengajuan-perjalanan/{$pengajuan->id_pengajuan}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
                ],
            ]);
    }

    public function test_can_update_status_workflow(): void
    {
        $pengajuan = PengajuanPerjalananTugas::where('status', StatusPengajuanEnum::DRAFT->value)->first();

        // 1. draft -> menunggu_persetujuan
        $response = $this->patchJson("/api/pengajuan-perjalanan/{$pengajuan->id_pengajuan}/status", [
            'status' => StatusPengajuanEnum::MENUNGGU_PERSETUJUAN->value,
            'catatan' => 'Mengajukan persetujuan',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'menunggu_persetujuan',
                ],
            ]);

        // 2. menunggu_persetujuan -> disetujui
        $response2 = $this->patchJson("/api/pengajuan-perjalanan/{$pengajuan->id_pengajuan}/status", [
            'status' => StatusPengajuanEnum::DISETUJUI->value,
            'catatan' => 'Persetujuan diberikan oleh manajer',
        ]);

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'disetujui',
                ],
            ]);

        // 3. disetujui -> draft (Invalid transition should return 422)
        $response3 = $this->patchJson("/api/pengajuan-perjalanan/{$pengajuan->id_pengajuan}/status", [
            'status' => StatusPengajuanEnum::DRAFT->value,
        ]);

        $response3->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_cannot_edit_approved_pengajuan(): void
    {
        $pengajuan = PengajuanPerjalananTugas::first();
        $pengajuan->update(['status' => StatusPengajuanEnum::DISETUJUI->value]);

        $response = $this->putJson("/api/pengajuan-perjalanan/{$pengajuan->id_pengajuan}", [
            'maksud_tujuan' => 'Mencoba merubah pengajuan yang sudah disetujui',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}
