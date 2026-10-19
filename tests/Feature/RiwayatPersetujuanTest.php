<?php

namespace Tests\Feature;

use App\Enums\ActionPersetujuanEnum;
use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\Karyawan;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\RiwayatPersetujuan;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPersetujuanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('email', 'admin@astronova.co.id')->first()
            ?? User::factory()->create([
                'name' => 'Manager Test',
                'email' => 'manager@astronova.co.id',
            ]);
    }

    /**
     * Helper: create a pengajuan perjalanan with given status.
     */
    protected function createPengajuanPerjalanan(string $status = 'menunggu_persetujuan'): PengajuanPerjalananTugas
    {
        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();

        return PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/9999',
            'id_karyawan' => $karyawan->id_karyawan,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(3)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(5)->format('Y-m-d'),
            'maksud_tujuan' => 'Perjalanan dinas testing approval',
            'estimasi_biaya' => 5000000,
            'uang_muka' => 3000000,
            'status' => $status,
        ]);
    }

    /**
     * Helper: create a pengajuan penggantian with given status.
     */
    protected function createPengajuanPenggantian(string $status = 'menunggu_persetujuan'): PengajuanPenggantian
    {
        $pengajuan = $this->createPengajuanPerjalanan('disetujui');

        return PengajuanPenggantian::create([
            'nomor_penggantian' => 'RMB/'.date('Ym').'/9999',
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'tanggal_pengajuan' => now()->format('Y-m-d'),
            'total_pengeluaran' => 4500000,
            'uang_muka' => 3000000,
            'selisih' => 1500000,
            'kondisi_penggantian' => 'kurang_bayar',
            'status' => $status,
        ]);
    }

    // ── APPROVE Tests ──────────────────────────────────────────────

    public function test_approve_pengajuan_perjalanan(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan();

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
                'nama_approver' => 'Manager Test',
                'peran_approver' => 'Manager',
                'catatan' => 'Disetujui, silakan lanjutkan.',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen berhasil disetujui.',
            ])
            ->assertJsonPath('data.status', 'disetujui');

        // Verify document status updated
        $pengajuan->refresh();
        $this->assertEquals(StatusPengajuanEnum::DISETUJUI, $pengajuan->status);

        // Verify riwayat created
        $this->assertDatabaseHas('riwayat_persetujuan', [
            'approvable_type' => 'pengajuan_perjalanan',
            'approvable_id' => $pengajuan->id_pengajuan,
            'status' => 'disetujui',
        ]);
    }

    public function test_approve_pengajuan_penggantian(): void
    {
        $penggantian = $this->createPengajuanPenggantian();

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'pengajuan_penggantian',
                'approvable_id' => $penggantian->id_penggantian,
                'nama_approver' => 'Manager Test',
                'peran_approver' => 'Manager',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $penggantian->refresh();
        $this->assertEquals(StatusPenggantianEnum::DISETUJUI, $penggantian->status);
    }

    // ── REJECT Tests ───────────────────────────────────────────────

    public function test_reject_pengajuan_perjalanan(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan();

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/reject', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
                'nama_approver' => 'Manager Test',
                'peran_approver' => 'Manager',
                'catatan' => 'Tujuan perjalanan kurang jelas, mohon diperjelas.',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen berhasil ditolak.',
            ])
            ->assertJsonPath('data.status', 'ditolak');

        $pengajuan->refresh();
        $this->assertEquals(StatusPengajuanEnum::DITOLAK, $pengajuan->status);
    }

    public function test_reject_requires_catatan(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan();

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/reject', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
                // catatan tidak dikirim
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    // ── VALIDATION Tests ───────────────────────────────────────────

    public function test_cannot_approve_draft_document(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan('draft');

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_cannot_approve_already_approved_document(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan('disetujui');

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_cannot_reject_already_rejected_document(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan('ditolak');

        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/reject', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => $pengajuan->id_pengajuan,
                'catatan' => 'Test reject',
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_approve_invalid_approvable_type(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'invalid_type',
                'approvable_id' => 1,
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_approve_nonexistent_document(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/persetujuan/approve', [
                'approvable_type' => 'pengajuan_perjalanan',
                'approvable_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    // ── AUTH Tests ──────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_approve(): void
    {
        $response = $this->postJson('/api/persetujuan/approve', [
            'approvable_type' => 'pengajuan_perjalanan',
            'approvable_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    // ── HISTORY Tests ──────────────────────────────────────────────

    public function test_get_approval_history(): void
    {
        $pengajuan = $this->createPengajuanPerjalanan();

        // Create riwayat manually
        RiwayatPersetujuan::create([
            'approvable_type' => 'pengajuan_perjalanan',
            'approvable_id' => $pengajuan->id_pengajuan,
            'id_user' => $this->user->id,
            'nama_approver' => 'Manager Test',
            'peran_approver' => 'Manager',
            'status' => ActionPersetujuanEnum::DISETUJUI->value,
            'catatan' => 'Disetujui.',
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/persetujuan/pengajuan_perjalanan/{$pengajuan->id_pengajuan}/history");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Riwayat persetujuan berhasil diambil.',
            ])
            ->assertJsonCount(1, 'data');
    }

    public function test_get_history_invalid_type(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/persetujuan/invalid_type/1/history');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_get_history_nonexistent_document(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/persetujuan/pengajuan_perjalanan/99999/history');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }
}
