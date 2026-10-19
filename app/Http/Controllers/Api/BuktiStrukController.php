<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BuktiStruk\UploadBuktiStrukRequest;
use App\Http\Resources\BuktiStrukResource;
use App\Services\BuktiStrukService;
use Exception;
use Illuminate\Http\JsonResponse;

class BuktiStrukController extends BaseApiController
{
    public function __construct(
        protected BuktiStrukService $service
    ) {}

    /**
     * Upload a new receipt file for an expense.
     *
     * POST /api/bukti-struk
     */
    public function store(UploadBuktiStrukRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $idPengeluaran = (int) $request->validated('id_pengeluaran');

            $buktiStruk = $this->service->upload($file, $idPengeluaran);

            return $this->sendSuccess(
                'Bukti struk berhasil diunggah.',
                new BuktiStrukResource($buktiStruk),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 400);
        }
    }

    /**
     * Delete a receipt file by ID.
     *
     * DELETE /api/bukti-struk/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->sendSuccess('Bukti struk berhasil dihapus.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }

    /**
     * Get list of receipt files for a specific expense.
     *
     * GET /api/pengeluaran/{idPengeluaran}/bukti-struk
     */
    public function indexByPengeluaran(int $idPengeluaran): JsonResponse
    {
        try {
            $list = $this->service->getByPengeluaran($idPengeluaran);

            return $this->sendSuccess(
                'Daftar bukti struk berhasil diambil.',
                BuktiStrukResource::collection($list)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }
}
