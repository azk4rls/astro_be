<?php

namespace Tests\Feature;

use App\Models\DokumenPendukung;
use App\Models\Karyawan;
use App\Models\PengajuanPerjalananTugas;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenPendukungTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected PengajuanPerjalananTugas $pengajuan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('email', 'admin@astronova.co.id')->first()
            ?? User::factory()->create([
                'name' => 'Manager Test',
                'email' => 'manager@astronova.co.id',
            ]);

        $karyawan = Karyawan::first();
        $tujuan = Tujuan::first();

        $this->pengajuan = PengajuanPerjalananTugas::create([
            'nomor_pengajuan' => 'TRV/'.date('Ym').'/8888',
            'id_karyawan' => $karyawan->id_karyawan,
            'id_tujuan' => $tujuan->id_tujuan,
            'tanggal_mulai' => now()->addDays(2)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(5)->format('Y-m-d'),
            'maksud_tujuan' => 'Perjalanan dinas audit cabang',
            'estimasi_biaya' => 7500000,
            'uang_muka' => 5000000,
            'status' => 'menunggu_persetujuan',
        ]);
    }

    // ── UPLOAD TESTS ──────────────────────────────────────────────────

    public function test_upload_dokumen_pendukung_pdf_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('surat_tugas.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'surat_tugas',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen pendukung berhasil diunggah.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_dokumen',
                    'id_pengajuan',
                    'nama_asli',
                    'mime_type',
                    'ukuran',
                    'jenis_dokumen',
                    'url',
                    'uploaded_at',
                ],
            ]);

        $path = DokumenPendukung::first()->path_file;
        Storage::disk('public')->assertExists($path);

        $this->assertDatabaseHas('dokumen_pendukung', [
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'nama_asli' => 'surat_tugas.pdf',
            'jenis_dokumen' => 'surat_tugas',
        ]);
    }

    public function test_upload_dokumen_pendukung_docx_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('itinerary.docx', 800, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'itinerary',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen pendukung berhasil diunggah.',
            ]);

        $path = DokumenPendukung::first()->path_file;
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_dokumen_pendukung_validation_file_required(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'undangan',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang dikirim.',
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_dokumen_pendukung_validation_invalid_mimes(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('installer.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'lainnya',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_dokumen_pendukung_validation_max_size_exceeded(): void
    {
        Storage::fake('public');

        // 12MB > max 10MB (10240 KB)
        $file = UploadedFile::fake()->create('large_doc.pdf', 12000, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'surat_tugas',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_dokumen_pendukung_validation_invalid_jenis_dokumen(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('undangan.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => $this->pengajuan->id_pengajuan,
                'jenis_dokumen' => 'invalid_jenis',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['jenis_dokumen']);
    }

    public function test_upload_dokumen_pendukung_validation_nonexistent_pengajuan(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/dokumen-pendukung', [
                'file' => $file,
                'id_pengajuan' => 99999,
                'jenis_dokumen' => 'surat_tugas',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['id_pengajuan']);
    }

    // ── DELETE TESTS ──────────────────────────────────────────────────

    public function test_delete_dokumen_pendukung_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('surat_tugas.pdf', 400, 'application/pdf');
        $path = $file->store('dokumen/2026/09', 'public');

        $dokumen = DokumenPendukung::create([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'path_file' => $path,
            'nama_asli' => 'surat_tugas.pdf',
            'mime_type' => 'application/pdf',
            'ukuran' => 409600,
            'jenis_dokumen' => 'surat_tugas',
            'uploaded_at' => now(),
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/dokumen-pendukung/{$dokumen->id_dokumen}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dokumen pendukung berhasil dihapus.',
            ]);

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('dokumen_pendukung', [
            'id_dokumen' => $dokumen->id_dokumen,
        ]);
    }

    public function test_delete_nonexistent_dokumen_pendukung(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/dokumen-pendukung/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Dokumen pendukung dengan ID 99999 tidak ditemukan.',
            ]);
    }

    // ── GET BY PENGAJUAN TESTS ────────────────────────────────────────

    public function test_get_dokumen_pendukung_by_pengajuan_success(): void
    {
        Storage::fake('public');

        DokumenPendukung::create([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'path_file' => 'dokumen/2026/09/surat.pdf',
            'nama_asli' => 'surat.pdf',
            'mime_type' => 'application/pdf',
            'ukuran' => 100000,
            'jenis_dokumen' => 'surat_tugas',
            'uploaded_at' => now(),
        ]);

        DokumenPendukung::create([
            'id_pengajuan' => $this->pengajuan->id_pengajuan,
            'path_file' => 'dokumen/2026/09/itinerary.pdf',
            'nama_asli' => 'itinerary.pdf',
            'mime_type' => 'application/pdf',
            'ukuran' => 200000,
            'jenis_dokumen' => 'itinerary',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/pengajuan-perjalanan/{$this->pengajuan->id_pengajuan}/dokumen");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Daftar dokumen pendukung berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_get_dokumen_pendukung_nonexistent_pengajuan(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/pengajuan-perjalanan/99999/dokumen');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data pengajuan perjalanan dengan ID 99999 tidak ditemukan.',
            ]);
    }

    // ── AUTH TESTS ────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_dokumen_pendukung_endpoints(): void
    {
        $responseStore = $this->postJson('/api/dokumen-pendukung', []);
        $responseStore->assertStatus(401);

        $responseDelete = $this->deleteJson('/api/dokumen-pendukung/1');
        $responseDelete->assertStatus(401);

        $responseGet = $this->getJson('/api/pengajuan-perjalanan/1/dokumen');
        $responseGet->assertStatus(401);
    }
}
