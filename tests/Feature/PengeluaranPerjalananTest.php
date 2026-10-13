<?php

namespace Tests\Feature;

use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PengeluaranPerjalananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    private function payload(array $overrides = []): array
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'UjiNull',
            'deskripsi' => 'Untuk pengujian.',
        ]);

        return array_merge([
            'id_pengajuan' => null,
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Pengujian null sudah_diganti',
            'jumlah' => 100000,
            'metode_pembayaran' => 'Cash',
        ], $overrides);
    }

    public function test_store_with_null_sudah_diganti_uses_default_false(): void
    {
        $response = $this->postJson('/api/pengeluaran-perjalanan', $this->payload(['sudah_diganti' => null]));

        $response->assertStatus(201)
            ->assertJsonPath('data.sudah_diganti', false);

        $this->assertDatabaseHas('pengeluaran_perjalanan', [
            'id_pengeluaran' => $response->json('data.id_pengeluaran'),
            'sudah_diganti' => false,
        ]);
    }

    public function test_store_with_true_sudah_diganti_stays_true(): void
    {
        $response = $this->postJson('/api/pengeluaran-perjalanan', $this->payload(['sudah_diganti' => true]));

        $response->assertStatus(201)
            ->assertJsonPath('data.sudah_diganti', true);
    }

    public function test_update_with_null_sudah_diganti_keeps_value(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'UpdateNull',
            'deskripsi' => 'Untuk pengujian update.',
        ]);
        $pengeluaran = PengeluaranPerjalanan::create([
            'id_pengajuan' => null,
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Awal',
            'jumlah' => 200000,
            'metode_pembayaran' => 'Transfer',
            'sudah_diganti' => true,
        ]);

        $response = $this->putJson("/api/pengeluaran-perjalanan/{$pengeluaran->id_pengeluaran}", [
            'deskripsi' => 'Diperbarui',
            'sudah_diganti' => null,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.sudah_diganti', true);

        $this->assertDatabaseHas('pengeluaran_perjalanan', [
            'id_pengeluaran' => $pengeluaran->id_pengeluaran,
            'sudah_diganti' => true,
        ]);
    }
}
