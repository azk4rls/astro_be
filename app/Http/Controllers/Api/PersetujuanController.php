<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Persetujuan\ApprovePersetujuanRequest;
use App\Http\Requests\Persetujuan\RejectPersetujuanRequest;
use App\Http\Resources\RiwayatPersetujuanResource;
use App\Services\PersetujuanService;
use Exception;
use Illuminate\Http\JsonResponse;

class PersetujuanController extends BaseApiController
{
    public function __construct(
        protected PersetujuanService $service
    ) {}

    /**
     * Approve a document (pengajuan_perjalanan / pengajuan_penggantian).
     *
     * POST /api/persetujuan/approve
     */
    public function approve(ApprovePersetujuanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Fallback: gunakan auth user jika tersedia, payload sebagai override untuk testing
            if ($request->user()) {
                $data['id_user'] = $data['id_user'] ?? $request->user()->id;
                $data['nama_approver'] = $data['nama_approver'] ?? $request->user()->name;
            }

            $riwayat = $this->service->approve($data);

            return $this->sendSuccess(
                'Dokumen berhasil disetujui.',
                new RiwayatPersetujuanResource($riwayat),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Reject a document (pengajuan_perjalanan / pengajuan_penggantian).
     *
     * POST /api/persetujuan/reject
     */
    public function reject(RejectPersetujuanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Fallback: gunakan auth user jika tersedia, payload sebagai override untuk testing
            if ($request->user()) {
                $data['id_user'] = $data['id_user'] ?? $request->user()->id;
                $data['nama_approver'] = $data['nama_approver'] ?? $request->user()->name;
            }

            $riwayat = $this->service->reject($data);

            return $this->sendSuccess(
                'Dokumen berhasil ditolak.',
                new RiwayatPersetujuanResource($riwayat),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Get approval history for a document.
     *
     * GET /api/persetujuan/{targetType}/{targetId}/history
     */
    public function history(string $targetType, int $targetId): JsonResponse
    {
        try {
            $riwayat = $this->service->getHistory($targetType, $targetId);

            return $this->sendSuccess(
                'Riwayat persetujuan berhasil diambil.',
                RiwayatPersetujuanResource::collection($riwayat)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }
}
