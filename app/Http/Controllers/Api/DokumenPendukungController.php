<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DokumenPendukung\UploadDokumenPendukungRequest;
use App\Http\Resources\DokumenPendukungResource;
use App\Services\DokumenPendukungService;
use Exception;
use Illuminate\Http\JsonResponse;

class DokumenPendukungController extends BaseApiController
{
    public function __construct(
        protected DokumenPendukungService $service
    ) {}

    /**
     * Upload a new supporting document for travel request.
     *
     * POST /api/dokumen-pendukung
     */
    public function store(UploadDokumenPendukungRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $idPengajuan = (int) $request->validated('id_pengajuan');
            $jenis = (string) $request->validated('jenis_dokumen');

            $dokumen = $this->service->upload($file, $idPengajuan, $jenis);

            return $this->sendSuccess(
                'Dokumen pendukung berhasil diunggah.',
                new DokumenPendukungResource($dokumen),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 400);
        }
    }

    /**
     * Delete a supporting document by ID.
     *
     * DELETE /api/dokumen-pendukung/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->sendSuccess('Dokumen pendukung berhasil dihapus.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }

    /**
     * Get list of supporting documents for a specific travel request.
     *
     * GET /api/pengajuan-perjalanan/{idPengajuan}/dokumen
     */
    public function indexByPengajuan(int $idPengajuan): JsonResponse
    {
        try {
            $list = $this->service->getByPengajuan($idPengajuan);

            return $this->sendSuccess(
                'Daftar dokumen pendukung berhasil diambil.',
                DokumenPendukungResource::collection($list)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }
}
