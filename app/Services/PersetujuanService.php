<?php

namespace App\Services;

use App\Enums\ActionPersetujuanEnum;
use App\Enums\StatusPengajuanEnum;
use App\Enums\StatusPenggantianEnum;
use App\Models\PengajuanPenggantian;
use App\Models\PengajuanPerjalananTugas;
use App\Models\RiwayatPersetujuan;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class PersetujuanService
{
    /**
     * Resolve the approvable model from morph map alias + ID.
     *
     * @throws Exception
     */
    protected function resolveApprovable(string $type, int $id): Model
    {
        $modelClass = Relation::getMorphedModel($type);

        if (! $modelClass) {
            throw new Exception("Tipe dokumen '{$type}' tidak dikenali.");
        }

        $model = $modelClass::find($id);

        if (! $model) {
            throw new Exception("Dokumen dengan tipe '{$type}' dan ID {$id} tidak ditemukan.");
        }

        return $model;
    }

    /**
     * Validate that the document status is 'menunggu_persetujuan'.
     *
     * @throws Exception
     */
    protected function validateStatusMenungguPersetujuan(Model $approvable): void
    {
        $status = $approvable->status;

        // Normalize to string for comparison
        $statusValue = $status instanceof StatusPengajuanEnum || $status instanceof StatusPenggantianEnum
            ? $status->value
            : (string) $status;

        if ($statusValue !== 'menunggu_persetujuan') {
            throw new Exception(
                "Dokumen tidak dapat diproses. Status saat ini: '{$statusValue}'. ".
                "Hanya dokumen dengan status 'menunggu_persetujuan' yang dapat di-approve/reject."
            );
        }
    }

    /**
     * Update the document status based on approval action.
     */
    protected function updateDocumentStatus(Model $approvable, ActionPersetujuanEnum $action): void
    {
        if ($approvable instanceof PengajuanPerjalananTugas) {
            $targetStatus = $action === ActionPersetujuanEnum::DISETUJUI
                ? StatusPengajuanEnum::DISETUJUI
                : StatusPengajuanEnum::DITOLAK;
            $approvable->update(['status' => $targetStatus->value]);
        } elseif ($approvable instanceof PengajuanPenggantian) {
            $targetStatus = $action === ActionPersetujuanEnum::DISETUJUI
                ? StatusPenggantianEnum::DISETUJUI
                : StatusPenggantianEnum::DITOLAK;
            $approvable->update(['status' => $targetStatus->value]);
        }
    }

    /**
     * Process approval (approve/reject) dalam 1 DB Transaction.
     *
     * @throws Exception
     */
    protected function processApproval(array $data, ActionPersetujuanEnum $action): RiwayatPersetujuan
    {
        return DB::transaction(function () use ($data, $action) {
            $approvable = $this->resolveApprovable($data['approvable_type'], $data['approvable_id']);

            $this->validateStatusMenungguPersetujuan($approvable);

            // Update status dokumen
            $this->updateDocumentStatus($approvable, $action);

            // Insert riwayat persetujuan
            $riwayat = RiwayatPersetujuan::create([
                'approvable_type' => $data['approvable_type'],
                'approvable_id' => $data['approvable_id'],
                'id_user' => $data['id_user'] ?? null,
                'nama_approver' => $data['nama_approver'] ?? null,
                'peran_approver' => $data['peran_approver'] ?? null,
                'status' => $action->value,
                'catatan' => $data['catatan'] ?? null,
                'approved_at' => now(),
            ]);

            return $riwayat->load('approvable');
        });
    }

    /**
     * Approve dokumen.
     *
     * @throws Exception
     */
    public function approve(array $data): RiwayatPersetujuan
    {
        return $this->processApproval($data, ActionPersetujuanEnum::DISETUJUI);
    }

    /**
     * Reject dokumen.
     *
     * @throws Exception
     */
    public function reject(array $data): RiwayatPersetujuan
    {
        return $this->processApproval($data, ActionPersetujuanEnum::DITOLAK);
    }

    /**
     * Get approval history for a specific document.
     *
     * @throws Exception
     */
    public function getHistory(string $targetType, int $targetId): Collection
    {
        // Validate that the target type is valid
        $modelClass = Relation::getMorphedModel($targetType);
        if (! $modelClass) {
            throw new Exception("Tipe dokumen '{$targetType}' tidak dikenali.");
        }

        // Validate that the document exists
        $model = $modelClass::find($targetId);
        if (! $model) {
            throw new Exception("Dokumen dengan tipe '{$targetType}' dan ID {$targetId} tidak ditemukan.");
        }

        return RiwayatPersetujuan::where('approvable_type', $targetType)
            ->where('approvable_id', $targetId)
            ->orderBy('approved_at', 'desc')
            ->get();
    }
}
