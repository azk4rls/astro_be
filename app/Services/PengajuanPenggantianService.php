<?php

namespace App\Services;

use App\Enums\KondisiPenggantianEnum;
use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PengajuanPenggantianService
{
    /**
     * Get paginated or list of pengajuan penggantian with filters.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = PengajuanPenggantian::with([
            'pengajuanPerjalanan.karyawan',
            'pengajuanPerjalanan.proyek',
            'pengajuanPerjalanan.tujuan',
            'details.kategori',
        ]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['kondisi_penggantian'])) {
            $query->where('kondisi_penggantian', $filters['kondisi_penggantian']);
        }

        if (! empty($filters['id_pengajuan'])) {
            $query->where('id_pengajuan', $filters['id_pengajuan']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_penggantian', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $query->latest('id_penggantian');

        if (isset($filters['paginate']) && $filters['paginate'] === 'false') {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get single pengajuan penggantian by ID with relationships.
     */
    public function getById(int $id): PengajuanPenggantian
    {
        return PengajuanPenggantian::with([
            'pengajuanPerjalanan.karyawan',
            'pengajuanPerjalanan.proyek',
            'pengajuanPerjalanan.tujuan',
            'details.kategori',
        ])->findOrFail($id);
    }

    /**
     * Generate unique nomor_penggantian RMB/YYYYMM/0001 (resets monthly).
     */
    public function generateNomorPenggantian(): string
    {
        $now = Carbon::now();
        $prefix = 'RMB/'.$now->format('Ym').'/';

        $lastPenggantian = PengajuanPenggantian::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('nomor_penggantian', 'like', $prefix.'%')
            ->orderBy('id_penggantian', 'desc')
            ->first();

        if ($lastPenggantian && preg_match('/'.preg_quote($prefix, '/').'(\d{4})$/', $lastPenggantian->nomor_penggantian, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create new pengajuan penggantian with automatic finance calculation.
     *
     * @throws Exception
     */
    public function create(array $data): PengajuanPenggantian
    {
        return DB::transaction(function () use ($data) {
            $pengajuanPerjalanan = PengajuanPerjalananTugas::findOrFail($data['id_pengajuan']);

            $statusPerjalanan = $pengajuanPerjalanan->status instanceof StatusPengajuanEnum
                ? $pengajuanPerjalanan->status
                : StatusPengajuanEnum::tryFrom($pengajuanPerjalanan->status);

            if ($statusPerjalanan !== StatusPengajuanEnum::DISETUJUI) {
                throw new Exception('Pengajuan penggantian hanya dapat dibuat untuk pengajuan perjalanan tugas yang telah disetujui.');
            }

            if (PengajuanPenggantian::where('id_pengajuan', $data['id_pengajuan'])->exists()) {
                throw new Exception('Pengajuan perjalanan tugas ini sudah memiliki dokumen pengajuan penggantian (reimbursement).');
            }

            if (empty($data['nomor_penggantian'])) {
                $data['nomor_penggantian'] = $this->generateNomorPenggantian();
            }

            if (empty($data['status'])) {
                $data['status'] = StatusPenggantianEnum::DRAFT->value;
            }

            $uangMuka = floatval($pengajuanPerjalanan->uang_muka ?? 0);
            $data['uang_muka'] = $uangMuka;

            $detailsData = $data['details'] ?? [];
            unset($data['details']);

            $totalPengeluaran = 0;
            foreach ($detailsData as $detail) {
                $totalPengeluaran += floatval($detail['jumlah'] ?? 0);
            }

            $selisih = $totalPengeluaran - $uangMuka;
            $kondisi = KondisiPenggantianEnum::fromSelisih($selisih);

            $data['total_pengeluaran'] = $totalPengeluaran;
            $data['selisih'] = $selisih;
            $data['kondisi_penggantian'] = $kondisi->value;

            $penggantian = PengajuanPenggantian::create($data);

            if (! empty($detailsData)) {
                foreach ($detailsData as $detail) {
                    $penggantian->details()->create([
                        'id_kategori' => $detail['id_kategori'],
                        'tanggal_pengeluaran' => $detail['tanggal_pengeluaran'],
                        'deskripsi' => $detail['deskripsi'] ?? null,
                        'jumlah' => $detail['jumlah'],
                        'bukti_file' => $detail['bukti_file'] ?? null,
                    ]);
                }
            }

            return $this->getById($penggantian->id_penggantian);
        });
    }

    /**
     * Update existing pengajuan penggantian.
     *
     * @throws Exception
     */
    public function update(PengajuanPenggantian $penggantian, array $data): PengajuanPenggantian
    {
        $currentStatus = $penggantian->status instanceof StatusPenggantianEnum
            ? $penggantian->status
            : StatusPenggantianEnum::tryFrom($penggantian->status);

        if (in_array($currentStatus, [StatusPenggantianEnum::DISETUJUI, StatusPenggantianEnum::DITOLAK])) {
            throw new Exception("Pengajuan penggantian yang sudah {$currentStatus->label()} tidak dapat diubah.");
        }

        return DB::transaction(function () use ($penggantian, $data) {
            $uangMuka = floatval($penggantian->pengajuanPerjalanan?->uang_muka ?? $penggantian->uang_muka);

            if (array_key_exists('details', $data)) {
                $detailsData = $data['details'] ?? [];
                unset($data['details']);

                $penggantian->details()->delete();

                $totalPengeluaran = 0;
                foreach ($detailsData as $detail) {
                    $penggantian->details()->create([
                        'id_kategori' => $detail['id_kategori'],
                        'tanggal_pengeluaran' => $detail['tanggal_pengeluaran'],
                        'deskripsi' => $detail['deskripsi'] ?? null,
                        'jumlah' => $detail['jumlah'],
                        'bukti_file' => $detail['bukti_file'] ?? null,
                    ]);
                    $totalPengeluaran += floatval($detail['jumlah'] ?? 0);
                }

                $selisih = $totalPengeluaran - $uangMuka;
                $kondisi = KondisiPenggantianEnum::fromSelisih($selisih);

                $data['total_pengeluaran'] = $totalPengeluaran;
                $data['selisih'] = $selisih;
                $data['kondisi_penggantian'] = $kondisi->value;
            }

            $penggantian->update($data);

            return $this->getById($penggantian->id_penggantian);
        });
    }

    /**
     * Update workflow status of pengajuan penggantian.
     *
     * @throws Exception
     */
    public function updateStatus(PengajuanPenggantian $penggantian, StatusPenggantianEnum $targetStatus, ?string $catatan = null): PengajuanPenggantian
    {
        $currentStatus = $penggantian->status instanceof StatusPenggantianEnum
            ? $penggantian->status
            : StatusPenggantianEnum::from($penggantian->status);

        if (! $currentStatus->canTransitionTo($targetStatus)) {
            throw new Exception("Perubahan status dari '{$currentStatus->label()}' ke '{$targetStatus->label()}' tidak diperbolehkan.");
        }

        $updateData = ['status' => $targetStatus->value];
        if ($catatan !== null) {
            $updateData['catatan'] = $catatan;
        }

        $penggantian->update($updateData);

        return $this->getById($penggantian->id_penggantian);
    }

    /**
     * Delete pengajuan penggantian.
     *
     * @throws Exception
     */
    public function delete(PengajuanPenggantian $penggantian): bool
    {
        $currentStatus = $penggantian->status instanceof StatusPenggantianEnum
            ? $penggantian->status
            : StatusPenggantianEnum::tryFrom($penggantian->status);

        if ($currentStatus === StatusPenggantianEnum::DISETUJUI) {
            throw new Exception('Pengajuan penggantian yang sudah disetujui tidak dapat dihapus.');
        }

        return (bool) $penggantian->delete();
    }
}
