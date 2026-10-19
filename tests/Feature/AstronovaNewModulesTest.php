<?php

namespace Tests\Feature;

use App\Models\PengajuanPerjalananTugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AstronovaNewModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_vendor_crud(): void
    {
        $this->getJson('/api/vendor')
            ->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => [['id_vendor', 'nama_vendor', 'kategori', 'rating']]]);

        $created = $this->postJson('/api/vendor', [
            'nama_vendor' => 'Lion Air',
            'kategori' => 'Maskapai',
            'kontak' => '021-63798000',
            'rating' => 3.9,
        ]);

        $created->assertStatus(201)->assertJson(['success' => true]);
        $id = $created->json('data.id_vendor');

        $this->putJson("/api/vendor/{$id}", ['nama_vendor' => 'Lion Air Group'])
            ->assertStatus(200)
            ->assertJsonPath('data.nama_vendor', 'Lion Air Group');

        $this->deleteJson("/api/vendor/{$id}")->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson("/api/vendor/{$id}")->assertStatus(404);
    }

    public function test_travel_policy_create_and_update(): void
    {
        $created = $this->postJson('/api/travel-policy', [
            'latarbelakang' => 'Perjalanan Dinas Training',
            'grade' => 'Staff',
            'limit_biaya' => 3000000,
            'kelas' => 'Ekonomi',
        ]);

        $created->assertStatus(201)->assertJson(['success' => true]);
        $id = $created->json('data.id_policy');

        $this->putJson("/api/travel-policy/{$id}", ['limit_biaya' => 3500000])
            ->assertStatus(200)
            ->assertJsonPath('data.limit_biaya', 3500000);
    }

    public function test_notifikasi_list_create_and_mark_all_read(): void
    {
        $this->getJson('/api/notifikasi')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id_notifikasi', 'judul', 'isi', 'dibaca']]]);

        $this->postJson('/api/notifikasi', [
            'judul' => 'Uji Notifikasi',
            'isi' => 'Isi notifikasi pengujian.',
        ])->assertStatus(201)->assertJson(['success' => true]);

        $this->patchJson('/api/notifikasi/read-all')
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $unread = collect($this->getJson('/api/notifikasi')->json('data'))->where('dibaca', false);
        $this->assertCount(0, $unread);
    }

    public function test_delegasi_create_and_list(): void
    {
        $penerima = User::query()->latest('id')->value('id');

        $this->postJson('/api/delegasi', [
            'id_penerima' => $penerima,
            'dari_tgl' => '2026-11-01',
            'sampai_tgl' => '2026-11-07',
            'alasan' => 'Dinas luar kota.',
        ])->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.penerima', User::find($penerima)->name);

        $this->getJson('/api/delegasi')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id_delegasi', 'pemberi', 'penerima', 'dari_tgl', 'sampai_tgl']]]);
    }

    public function test_itinerary_create_with_trip_filter(): void
    {
        $pengajuanId = PengajuanPerjalananTugas::query()->value('id_pengajuan');

        $this->postJson('/api/itinerary', [
            'id_pengajuan' => $pengajuanId,
            'waktu' => '2026-12-01 08:00:00',
            'aktivitas' => 'Berangkat menuju lokasi',
            'lokasi' => 'Bandara Soekarno-Hatta',
        ])->assertStatus(201)->assertJson(['success' => true]);

        $this->getJson("/api/itinerary?id_pengajuan={$pengajuanId}")
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_booking_create_and_update_status(): void
    {
        $pengajuanId = PengajuanPerjalananTugas::query()->value('id_pengajuan');

        $created = $this->postJson('/api/booking', [
            'id_pengajuan' => $pengajuanId,
            'jenis' => 'Hotel',
            'detail' => 'Hotel Transit Bandung 2 Malam',
            'nomor_ref' => 'HTL-TEST-001',
            'vendor' => 'Aston Inn',
            'harga' => 900000,
        ]);

        $created->assertStatus(201)->assertJson(['success' => true]);
        $id = $created->json('data.id_booking');

        $this->patchJson("/api/booking/{$id}/status", ['status' => 'confirmed'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');
    }

    public function test_uang_muka_cairkan(): void
    {
        $pengajuanId = PengajuanPerjalananTugas::query()->value('id_pengajuan');

        $created = $this->postJson('/api/uang-muka', [
            'id_pengajuan' => $pengajuanId,
            'diajukan' => 1500000,
            'tanggal' => '2026-12-01',
        ]);

        $created->assertStatus(201)->assertJsonPath('data.status', 'diajukan');
        $id = $created->json('data.id_uang_muka');

        $this->postJson("/api/uang-muka/{$id}/cairkan")
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'dicairkan')
            ->assertJsonPath('data.disetujui', 1500000);
    }

    public function test_settlement_create_and_update_status(): void
    {
        $pengajuanId = PengajuanPerjalananTugas::query()->value('id_pengajuan');

        $created = $this->postJson('/api/settlement', [
            'id_pengajuan' => $pengajuanId,
            'total_realisasi' => 2750000,
        ]);

        $created->assertStatus(201)->assertJsonPath('data.status', 'diajukan');
        $id = $created->json('data.id_settlement');

        $this->patchJson("/api/settlement/{$id}/status", ['status' => 'diverifikasi'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'diverifikasi');
    }

    public function test_pengguna_crud(): void
    {
        $this->getJson('/api/pengguna')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'email', 'role']]]);

        $created = $this->postJson('/api/pengguna', [
            'name' => 'Pengguna Uji',
            'email' => 'pengguna.uji@astronova.co.id',
            'password' => 'password123',
            'role' => 'karyawan',
            'jabatan' => 'Analis',
        ]);

        $created->assertStatus(201)->assertJson(['success' => true]);
        $id = $created->json('data.id');

        $this->putJson("/api/pengguna/{$id}", ['jabatan' => 'Analis Senior'])
            ->assertStatus(200)
            ->assertJsonPath('data.jabatan', 'Analis Senior');

        $this->deleteJson("/api/pengguna/{$id}")->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson("/api/pengguna/{$id}")->assertStatus(404);
    }
}
