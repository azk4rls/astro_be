<?php

namespace App\Services;

use App\Enums\StatusPengajuanEnum;
use App\Models\PengajuanPerjalananTugas;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PengajuanPerjalananService
{
    /**
     * Get paginated or list of pengajuan perjalanan with filters.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = PengajuanPerjalananTugas::with([
            'karyawan',
            'proyek',
            'tujuan',
            'details.kategori',
        ]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['id_karyawan'])) {
            $query->where('id_karyawan', $filters['id_karyawan']);
        }

        if (! empty($filters['id_proyek'])) {
            $query->where('id_proyek', $filters['id_proyek']);
        }

        if (! empty($filters['id_tujuan'])) {
            $query->where('id_tujuan', $filters['id_tujuan']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('maksud_tujuan', 'like', "%{$search}%");
            });
        }

        $query->latest('id_pengajuan');

        if (isset($filters['paginate']) && $filters['paginate'] === 'false') {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get single pengajuan by ID with relationships.
     */
    public function getById(int $id): PengajuanPerjalananTugas
    {
        return PengajuanPerjalananTugas::with([
            'karyawan',
            'proyek',
            'tujuan',
            'details.kategori',
        ])->findOrFail($id);
    }

    /**
     * Generate unique nomor_pengajuan TRV/YYYYMM/0001 (resets monthly).
     */
    public function generateNomorPengajuan(): string
    {
        $now = Carbon::now();
        $prefix = 'TRV/'.$now->format('Ym').'/';

        $lastPengajuan = PengajuanPerjalananTugas::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('nomor_pengajuan', 'like', $prefix.'%')
            ->orderBy('id_pengajuan', 'desc')
            ->first();

        if ($lastPengajuan && preg_match('/'.preg_quote($prefix, '/').'(\d{4})$/', $lastPengajuan->nomor_pengajuan, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create new pengajuan perjalanan.
     *
     * @throws Exception
     */
    public function create(array $data): PengajuanPerjalananTugas
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['nomor_pengajuan'])) {
                $data['nomor_pengajuan'] = $this->generateNomorPengajuan();
            }

            if (empty($data['status'])) {
                $data['status'] = StatusPengajuanEnum::DRAFT->value;
            }

            $detailsData = $data['details'] ?? [];
            unset($data['details']);

            $totalEstimasi = 0;
            foreach ($detailsData as $detail) {
                $totalEstimasi += floatval($detail['estimasi_biaya'] ?? 0);
            }

            if (! isset($data['estimasi_biaya']) || $totalEstimasi > 0) {
                $data['estimasi_biaya'] = $totalEstimasi;
            }

            $pengajuan = PengajuanPerjalananTugas::create($data);

            if (! empty($detailsData)) {
                foreach ($detailsData as $detail) {
                    $pengajuan->details()->create([
                        'id_kategori' => $detail['id_kategori'],
                        'estimasi_biaya' => $detail['estimasi_biaya'],
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                }
            }

            return $this->getById($pengajuan->id_pengajuan);
        });
    }

    /**
     * Update existing pengajuan perjalanan.
     *
     * @throws Exception
     */
    public function update(PengajuanPerjalananTugas $pengajuan, array $data): PengajuanPerjalananTugas
    {
        $currentStatus = $pengajuan->status instanceof StatusPengajuanEnum
            ? $pengajuan->status
            : StatusPengajuanEnum::tryFrom($pengajuan->status);

        if (in_array($currentStatus, [StatusPengajuanEnum::DISETUJUI, StatusPengajuanEnum::DITOLAK])) {
            throw new Exception("Pengajuan yang sudah {$currentStatus->label()} tidak dapat diubah.");
        }

        return DB::transaction(function () use ($pengajuan, $data) {
            if (array_key_exists('details', $data)) {
                $detailsData = $data['details'] ?? [];
                unset($data['details']);

                $pengajuan->details()->delete();

                $totalEstimasi = 0;
                foreach ($detailsData as $detail) {
                    $pengajuan->details()->create([
                        'id_kategori' => $detail['id_kategori'],
                        'estimasi_biaya' => $detail['estimasi_biaya'],
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                    $totalEstimasi += floatval($detail['estimasi_biaya'] ?? 0);
                }

                if (count($detailsData) > 0) {
                    $data['estimasi_biaya'] = $totalEstimasi;
                }
            }

            $pengajuan->update($data);

            return $this->getById($pengajuan->id_pengajuan);
        });
    }

    /**
     * Update workflow status of pengajuan.
     *
     * @throws Exception
     */
    public function updateStatus(PengajuanPerjalananTugas $pengajuan, StatusPengajuanEnum $targetStatus, ?string $catatan = null): PengajuanPerjalananTugas
    {
        $currentStatus = $pengajuan->status instanceof StatusPengajuanEnum
            ? $pengajuan->status
            : StatusPengajuanEnum::from($pengajuan->status);

        if (! $currentStatus->canTransitionTo($targetStatus)) {
            throw new Exception("Perubahan status dari '{$currentStatus->label()}' ke '{$targetStatus->label()}' tidak diperbolehkan.");
        }

        $updateData = ['status' => $targetStatus->value];
        if ($catatan !== null) {
            $updateData['catatan'] = $catatan;
        }

        $pengajuan->update($updateData);

        return $this->getById($pengajuan->id_pengajuan);
    }

    /**
     * Delete pengajuan.
     *
     * @throws Exception
     */
    public function delete(PengajuanPerjalananTugas $pengajuan): bool
    {
        $currentStatus = $pengajuan->status instanceof StatusPengajuanEnum
            ? $pengajuan->status
            : StatusPengajuanEnum::tryFrom($pengajuan->status);

        if ($currentStatus === StatusPengajuanEnum::DISETUJUI) {
            throw new Exception('Pengajuan yang sudah disetujui tidak dapat dihapus.');
        }

        return (bool) $pengajuan->delete();
    }
}
