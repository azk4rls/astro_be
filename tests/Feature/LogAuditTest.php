<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\LogAudit;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Karyawan $karyawan;

    protected Tujuan $tujuan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('email', 'admin@astronova.co.id')->first()
            ?? User::factory()->create([
                'name' => 'Manager Test',
                'email' => 'manager@astronova.co.id',
            ]);

        $this->karyawan = Karyawan::first();
        $this->tujuan = Tujuan::first();
    }

    // ── OBSERVER LOGIC TESTS ──────────────────────────────────────────

    public function test_model_created_event_records_log_audit(): void
    {
        $this->actingAs($this->user);

        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7777',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Testing audit log created',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'menunggu_persetujuan',
        ]);

        $this->assertDatabaseHas('log_audit', [
            'model_type' => 'pengajuan_perjalanan',
            'model_id' => $pengajuan->id_pengajuan,
            'action' => 'created',
            'id_user' => $this->user->id,
        ]);

        $log = LogAudit::where('model_type', 'pengajuan_perjalanan')
            ->where('model_id', $pengajuan->id_pengajuan)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Testing audit log created', $log->new_values['maksud_tujuan']);
    }

    public function test_model_updated_event_records_log_audit_with_changed_values(): void
    {
        $this->actingAs($this->user);

        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7778',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Maksud awal',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'menunggu_persetujuan',
        ]);

        $pengajuan->update([
            'maksud_tujuan' => 'Maksud baru diperbarui',
            'estimasi_biaya' => 3000000,
        ]);

        $this->assertDatabaseHas('log_audit', [
            'model_type' => 'pengajuan_perjalanan',
            'model_id' => $pengajuan->id_pengajuan,
            'action' => 'updated',
        ]);

        $log = LogAudit::where('model_type', 'pengajuan_perjalanan')
            ->where('model_id', $pengajuan->id_pengajuan)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Maksud awal', $log->old_values['maksud_tujuan']);
        $this->assertEquals('Maksud baru diperbarui', $log->new_values['maksud_tujuan']);
        $this->assertArrayNotHasKey('updated_at', $log->new_values);
    }

    public function test_model_updated_only_updated_at_skips_log_audit(): void
    {
        $this->actingAs($this->user);

        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7779',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Testing skip touch',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'menunggu_persetujuan',
        ]);

        $initialLogCount = LogAudit::where('model_type', 'pengajuan_perjalanan')
            ->where('model_id', $pengajuan->id_pengajuan)
            ->count();

        // Touch updates only updated_at
        $pengajuan->touch();

        $afterTouchLogCount = LogAudit::where('model_type', 'pengajuan_perjalanan')
            ->where('model_id', $pengajuan->id_pengajuan)
            ->count();

        $this->assertEquals($initialLogCount, $afterTouchLogCount);
    }

    public function test_model_deleted_event_records_log_audit(): void
    {
        $this->actingAs($this->user);

        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7780',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Akan dihapus',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'draft',
        ]);

        $id = $pengajuan->id_pengajuan;
        $pengajuan->delete();

        $this->assertDatabaseHas('log_audit', [
            'model_type' => 'pengajuan_perjalanan',
            'model_id' => $id,
            'action' => 'deleted',
        ]);

        $log = LogAudit::where('model_type', 'pengajuan_perjalanan')
            ->where('model_id', $id)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Akan dihapus', $log->old_values['maksud_tujuan']);
    }

    // ── ENDPOINT TESTS ────────────────────────────────────────────────

    public function test_get_all_log_audits_endpoint_with_pagination_and_filters(): void
    {
        $this->actingAs($this->user);

        PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7781',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Testing index endpoint',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/log-audit?action=created&model_type=pengajuan_perjalanan');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data log audit berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id_log',
                            'id_user',
                            'nama_user',
                            'model_type',
                            'model_id',
                            'action',
                            'old_values',
                            'new_values',
                            'ip_address',
                            'user_agent',
                            'created_at',
                        ],
                    ],
                    'links',
                    'meta',
                ],
            ]);
    }

    public function test_get_history_log_audit_endpoint(): void
    {
        $this->actingAs($this->user);

        $pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/7782',
            'id_karyawan' => $this->karyawan->id_karyawan,
            'id_tujuan' => $this->tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'maksud_tujuan' => 'Testing history endpoint',
            'estimasi_biaya' => 2000000,
            'uang_muka' => 1000000,
            'status' => 'draft',
        ]);

        $pengajuan->update(['maksud_tujuan' => 'Testing history endpoint updated']);

        $response = $this->getJson("/api/log-audit/pengajuan_perjalanan/{$pengajuan->id_pengajuan}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Riwayat audit model berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_user_cannot_access_log_audit_endpoints(): void
    {
        $responseIndex = $this->getJson('/api/log-audit');
        $responseIndex->assertStatus(401);

        $responseHistory = $this->getJson('/api/log-audit/pengajuan_perjalanan/1');
        $responseHistory->assertStatus(401);
    }
}
