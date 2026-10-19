<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranPerjalanan;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_cannot_delete_departemen_with_karyawan(): void
    {
        $departemen = Departemen::create(['nama_departemen' => 'Keuangan']);
        $karyawan = Karyawan::create([
            'nik' => 'KRY-DEL-001',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi.santoso@astronova.co.id',
            'telepon' => '081111111111',
            'id_departemen' => $departemen->id_departemen,
            'peran' => 'Staff',
        ]);

        $response = $this->deleteJson("/api/departemen/{$departemen->id_departemen}");

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Departemen tidak dapat dihapus karena masih memiliki karyawan.',
            ]);

        $this->assertDatabaseHas('departemen', ['id_departemen' => $departemen->id_departemen]);
        $this->assertDatabaseHas('karyawan', ['id_karyawan' => $karyawan->id_karyawan]);
    }

    public function test_database_restricts_departemen_delete_with_children(): void
    {
        $departemen = Departemen::create(['nama_departemen' => 'Operasional']);
        Karyawan::create([
            'nik' => 'KRY-DEL-002',
            'nama_lengkap' => 'Siti Aminah',
            'email' => 'siti.aminah@astronova.co.id',
            'telepon' => '081222222222',
            'id_departemen' => $departemen->id_departemen,
            'peran' => 'Staff',
        ]);

        $this->expectException(QueryException::class);

        $departemen->delete();
    }

    public function test_can_delete_empty_departemen(): void
    {
        $departemen = Departemen::create(['nama_departemen' => 'Legal Kosong']);

        $response = $this->deleteJson("/api/departemen/{$departemen->id_departemen}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('departemen', ['id_departemen' => $departemen->id_departemen]);
    }

    public function test_cannot_delete_kategori_with_pengeluaran(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'Transportasi',
            'deskripsi' => 'Biaya transport dinas.',
        ]);
        $pengeluaran = PengeluaranPerjalanan::create([
            'id_pengajuan' => null,
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Taksi bandara',
            'jumlah' => 150000,
            'metode_pembayaran' => 'Cash',
            'sudah_diganti' => false,
        ]);

        $response = $this->deleteJson("/api/kategori-pengeluaran/{$kategori->id_kategori}");

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Kategori pengeluaran tidak dapat dihapus karena masih digunakan oleh data pengeluaran.',
            ]);

        $this->assertDatabaseHas('kategori_pengeluaran', ['id_kategori' => $kategori->id_kategori]);
        $this->assertDatabaseHas('pengeluaran_perjalanan', ['id_pengeluaran' => $pengeluaran->id_pengeluaran]);
    }

    public function test_database_restricts_kategori_delete_with_children(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'Akomodasi',
            'deskripsi' => 'Biaya hotel dinas.',
        ]);
        PengeluaranPerjalanan::create([
            'id_pengajuan' => null,
            'id_kategori' => $kategori->id_kategori,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Hotel semalam',
            'jumlah' => 500000,
            'metode_pembayaran' => 'Transfer',
            'sudah_diganti' => false,
        ]);

        $this->expectException(QueryException::class);

        $kategori->delete();
    }

    public function test_can_delete_empty_kategori(): void
    {
        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => 'Kategori Kosong',
            'deskripsi' => 'Tidak dipakai.',
        ]);

        $response = $this->deleteJson("/api/kategori-pengeluaran/{$kategori->id_kategori}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('kategori_pengeluaran', ['id_kategori' => $kategori->id_kategori]);
    }
}
