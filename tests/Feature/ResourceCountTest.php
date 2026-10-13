<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResourceCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_departemen_index_includes_karyawan_count(): void
    {
        $departemen = Departemen::create(['nama_departemen' => 'Riset']);
        Karyawan::create([
            'nik' => 'KRY-CNT-001',
            'nama_lengkap' => 'Andi Pratama',
            'email' => 'andi.pratama@astronova.co.id',
            'telepon' => '081333333333',
            'id_departemen' => $departemen->id_departemen,
            'peran' => 'Staff',
        ]);
        Karyawan::create([
            'nik' => 'KRY-CNT-002',
            'nama_lengkap' => 'Dewi Lestari',
            'email' => 'dewi.lestari@astronova.co.id',
            'telepon' => '081444444444',
            'id_departemen' => $departemen->id_departemen,
            'peran' => 'Staff',
        ]);

        $response = $this->getJson('/api/departemen');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.karyawan_count', 2);
    }

    public function test_departemen_show_excludes_karyawan_count(): void
    {
        $departemen = Departemen::create(['nama_departemen' => 'Riset']);
        Karyawan::create([
            'nik' => 'KRY-CNT-003',
            'nama_lengkap' => 'Eko Saputra',
            'email' => 'eko.saputra@astronova.co.id',
            'telepon' => '081555555555',
            'id_departemen' => $departemen->id_departemen,
            'peran' => 'Staff',
        ]);

        $response = $this->getJson("/api/departemen/{$departemen->id_departemen}");

        $response->assertStatus(200);
        $this->assertArrayNotHasKey('karyawan_count', $response->json('data'));
    }

    public function test_kategori_index_includes_pengeluaran_count(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'Konsumsi',
            'deskripsi' => 'Biaya konsumsi rapat.',
        ]);
        PengeluaranPerjalanan::create([
            'id_pengajuan' => null,
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Snack rapat',
            'jumlah' => 75000,
            'metode_pembayaran' => 'Cash',
            'sudah_diganti' => false,
        ]);

        $response = $this->getJson('/api/kategori-pengeluaran');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.pengeluaran_perjalanan_count', 1);
    }

    public function test_kategori_show_excludes_pengeluaran_count(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'Konsumsi',
            'deskripsi' => 'Biaya konsumsi rapat.',
        ]);

        $response = $this->getJson("/api/kategori-pengeluaran/{$kategori->id_kategori}");

        $response->assertStatus(200);
        $this->assertArrayNotHasKey('pengeluaran_perjalanan_count', $response->json('data'));
    }
}
