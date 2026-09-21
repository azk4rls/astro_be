<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AstronovaMasterDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sequence:
     * 1. Create Departemen
     * 2. Create Karyawan
     * 3. Create Proyek
     * 4. Create Tujuan
     * 5. Create Kategori
     * 6. Create Pengeluaran
     */
    public function test_api_sequence_creation_and_standard_responses(): void
    {
        // 1. Create Departemen
        $deptResponse = $this->postJson('/api/departemen', [
            'nama_departemen' => 'Human Capital & General Affairs',
        ]);

        $deptResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_departemen',
                    'nama_departemen',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $deptId = $deptResponse->json('data.id_departemen');

        // 2. Create Karyawan
        $karyawanResponse = $this->postJson('/api/karyawan', [
            'nik' => 'KRY-TEST-999',
            'nama_lengkap' => 'Rina Wijaya',
            'email' => 'rina.wijaya@astronova.co.id',
            'telepon' => '081122334455',
            'id_departemen' => $deptId,
            'peran' => 'HR Specialist',
        ]);

        $karyawanResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_karyawan',
                    'nik',
                    'nama_lengkap',
                    'email',
                    'telepon',
                    'id_departemen',
                    'peran',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        // 3. Create Proyek
        $proyekResponse = $this->postJson('/api/proyek', [
            'nama_proyek' => 'Cloud Migration Phase 1',
            'kode_proyek' => 'PROJ-CMP-01',
        ]);

        $proyekResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_proyek',
                    'nama_proyek',
                    'kode_proyek',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        // 4. Create Tujuan
        $tujuanResponse = $this->postJson('/api/tujuan', [
            'nama_tujuan' => 'Kantor Regional Yogyakarta',
            'kota' => 'Yogyakarta',
            'negara' => 'Indonesia',
        ]);

        $tujuanResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_tujuan',
                    'nama_tujuan',
                    'kota',
                    'negara',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        // 5. Create Kategori Pengeluaran
        $kategoriResponse = $this->postJson('/api/kategori-pengeluaran', [
            'nama_kategori' => 'Entertainment Klien',
            'deskripsi' => 'Biaya jamuan makan malam bersama representatif klien.',
        ]);

        $kategoriResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_kategori',
                    'nama_kategori',
                    'deskripsi',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        $kategoriId = $kategoriResponse->json('data.id_kategori');

        // 6. Create Pengeluaran Perjalanan
        $pengeluaranResponse = $this->postJson('/api/pengeluaran-perjalanan', [
            'id_pengajuan' => 99,
            'id_kategori' => $kategoriId,
            'tanggal_pengeluaran' => '2026-09-08',
            'deskripsi' => 'Dinner meeting dengan direksi mitra',
            'jumlah' => 850000.50,
            'metode_pembayaran' => 'Corporate Card',
            'sudah_diganti' => true,
        ]);

        $pengeluaranResponse->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id_pengeluaran',
                    'id_pengajuan',
                    'id_kategori',
                    'tanggal_pengeluaran',
                    'deskripsi',
                    'jumlah',
                    'metode_pembayaran',
                    'sudah_diganti',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test validation error response format.
     */
    public function test_validation_error_standard_response(): void
    {
        $response = $this->postJson('/api/departemen', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'nama_departemen',
                ],
            ])
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test 404 error standard response.
     */
    public function test_not_found_standard_response(): void
    {
        $response = $this->getJson('/api/departemen/999999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Departemen tidak ditemukan.',
            ]);
    }
}
