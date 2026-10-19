<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPenggantianEnum;
use App\Http\Requests\PengajuanPenggantian\StorePengajuanPenggantianRequest;
use App\Http\Requests\PengajuanPenggantian\UpdatePengajuanPenggantianRequest;
use App\Http\Requests\PengajuanPenggantian\UpdateStatusPenggantianRequest;
use App\Http\Resources\PengajuanPenggantianResource;
use App\Services\PengajuanPenggantianService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanPenggantianController extends BaseApiController
{
    public function __construct(
        protected PengajuanPenggantianService $service
    ) {}

    /**
     * Display a listing of pengajuan penggantian.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'kondisi_penggantian', 'id_pengajuan', 'search', 'paginate']);
        $perPage = (int) $request->get('per_page', 15);

        $result = $this->service->getAll($filters, $perPage);

        if ($result instanceof LengthAwarePaginator) {
            $data = PengajuanPenggantianResource::collection($result)->response()->getData(true);

            return $this->sendSuccess('Data pengajuan penggantian berhasil diambil.', $data);
        }

        return $this->sendSuccess('Data pengajuan penggantian berhasil diambil.', PengajuanPenggantianResource::collection($result));
    }

    /**
     * Store a newly created pengajuan penggantian.
     */
    public function store(StorePengajuanPenggantianRequest $request): JsonResponse
    {
        try {
            $penggantian = $this->service->create($request->validated());

            return $this->sendSuccess(
                'Pengajuan penggantian (reimbursement) berhasil dibuat.',
                new PengajuanPenggantianResource($penggantian),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 400);
        }
    }

    /**
     * Display the specified pengajuan penggantian.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $penggantian = $this->service->getById($id);

            return $this->sendSuccess(
                'Detail pengajuan penggantian berhasil diambil.',
                new PengajuanPenggantianResource($penggantian)
            );
        } catch (Exception $e) {
            return $this->sendError('Data pengajuan penggantian tidak ditemukan.', null, 404);
        }
    }

    /**
     * Update the specified pengajuan penggantian.
     */
    public function update(UpdatePengajuanPenggantianRequest $request, int $id): JsonResponse
    {
        try {
            $penggantian = $this->service->getById($id);
            $updated = $this->service->update($penggantian, $request->validated());

            return $this->sendSuccess(
                'Pengajuan penggantian berhasil diperbarui.',
                new PengajuanPenggantianResource($updated)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Remove the specified pengajuan penggantian.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $penggantian = $this->service->getById($id);
            $this->service->delete($penggantian);

            return $this->sendSuccess('Pengajuan penggantian berhasil dihapus.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Update the workflow status of pengajuan penggantian.
     */
    public function updateStatus(UpdateStatusPenggantianRequest $request, int $id): JsonResponse
    {
        try {
            $penggantian = $this->service->getById($id);
            $targetStatus = StatusPenggantianEnum::from($request->validated('status'));
            $updated = $this->service->updateStatus($penggantian, $targetStatus, $request->validated('catatan'));

            return $this->sendSuccess(
                'Status pengajuan penggantian berhasil diperbarui.',
                new PengajuanPenggantianResource($updated)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }
}
