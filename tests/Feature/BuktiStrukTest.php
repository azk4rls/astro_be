<?php

namespace Tests\Feature;

use App\Models\BuktiStruk;
use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuktiStrukTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected PengeluaranPerjalanan $pengeluaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('email', 'admin@astronova.co.id')->first()
            ?? User::factory()->create([
                'name' => 'Manager Test',
                'email' => 'manager@astronova.co.id',
            ]);

        $kategori = KategoriPengeluaran::first() ?? KategoriPengeluaran::create([
            'nama_kategori' => 'Transportasi',
            'deskripsi' => 'Biaya transportasi lokal',
        ]);

        $this->pengeluaran = PengeluaranPerjalanan::create([
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => now()->format('Y-m-d'),
            'deskripsi' => 'Taksi bandara ke hotel',
            'jumlah' => 150000,
            'metode_pembayaran' => 'Cash',
            'sudah_diganti' => false,
        ]);
    }

    // ── UPLOAD TESTS ──────────────────────────────────────────────────

    public function test_upload_bukti_struk_jpg_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('struk_taksi.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'file' => $file,
                'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Bukti struk berhasil diunggah.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_bukti_struk',
                    'id_pengeluaran',
                    'nama_asli',
                    'mime_type',
                    'ukuran',
                    'url',
                    'uploaded_at',
                ],
            ]);

        $path = BuktiStruk::first()->path_file;
        Storage::disk('public')->assertExists($path);

        $this->assertDatabaseHas('bukti_struk', [
            'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            'nama_asli' => 'struk_taksi.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_upload_bukti_struk_pdf_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('hotel_invoice.pdf', 1200, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'file' => $file,
                'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Bukti struk berhasil diunggah.',
            ]);

        $path = BuktiStruk::first()->path_file;
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_bukti_struk_validation_file_required(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali data yang dikirim.',
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_bukti_struk_validation_invalid_mimes(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('invoice.zip', 100, 'application/zip');

        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'file' => $file,
                'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_bukti_struk_validation_max_size_exceeded(): void
    {
        Storage::fake('public');

        // 6MB > max 5MB (5120 KB)
        $file = UploadedFile::fake()->create('huge_receipt.jpg', 6000, 'image/jpeg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'file' => $file,
                'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_bukti_struk_validation_nonexistent_pengeluaran(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('receipt.png', 300, 'image/png');

        $response = $this->actingAs($this->user)
            ->postJson('/api/bukti-struk', [
                'file' => $file,
                'id_pengeluaran' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['id_pengeluaran']);
    }

    // ── DELETE TESTS ──────────────────────────────────────────────────

    public function test_delete_bukti_struk_success(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('struk_makan.jpg', 400, 'image/jpeg');
        $path = $file->store('struk/2026/09', 'public');

        $bukti = BuktiStruk::create([
            'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            'path_file' => $path,
            'nama_asli' => 'struk_makan.jpg',
            'mime_type' => 'image/jpeg',
            'ukuran' => 409600,
            'uploaded_at' => now(),
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/bukti-struk/{$bukti->id_bukti_struk}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Bukti struk berhasil dihapus.',
            ]);

        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('bukti_struk', [
            'id_bukti_struk' => $bukti->id_bukti_struk,
        ]);
    }

    public function test_delete_nonexistent_bukti_struk(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/bukti-struk/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Bukti struk dengan ID 99999 tidak ditemukan.',
            ]);
    }

    // ── GET BY PENGELUARAN TESTS ──────────────────────────────────────

    public function test_get_bukti_struk_by_pengeluaran_success(): void
    {
        Storage::fake('public');

        BuktiStruk::create([
            'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            'path_file' => 'struk/2026/09/struk1.jpg',
            'nama_asli' => 'struk1.jpg',
            'mime_type' => 'image/jpeg',
            'ukuran' => 100000,
            'uploaded_at' => now(),
        ]);

        BuktiStruk::create([
            'id_pengeluaran' => $this->pengeluaran->id_pengeluaran,
            'path_file' => 'struk/2026/09/struk2.pdf',
            'nama_asli' => 'struk2.pdf',
            'mime_type' => 'application/pdf',
            'ukuran' => 200000,
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/pengeluaran/{$this->pengeluaran->id_pengeluaran}/bukti-struk");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Daftar bukti struk berhasil diambil.',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_get_bukti_struk_nonexistent_pengeluaran(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/pengeluaran/99999/bukti-struk');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Data pengeluaran dengan ID 99999 tidak ditemukan.',
            ]);
    }

    // ── AUTH TESTS ────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_bukti_struk_endpoints(): void
    {
        $responseStore = $this->postJson('/api/bukti-struk', []);
        $responseStore->assertStatus(401);

        $responseDelete = $this->deleteJson('/api/bukti-struk/1');
        $responseDelete->assertStatus(401);

        $responseGet = $this->getJson('/api/pengeluaran/1/bukti-struk');
        $responseGet->assertStatus(401);
    }
}
