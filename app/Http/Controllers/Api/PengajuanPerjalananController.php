<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPengajuanEnum;
use App\Http\Requests\PengajuanPerjalanan\StorePengajuanPerjalananRequest;
use App\Http\Requests\PengajuanPerjalanan\UpdatePengajuanPerjalananRequest;
use App\Http\Requests\PengajuanPerjalanan\UpdateStatusPengajuanRequest;
use App\Http\Resources\PengajuanPerjalananResource;
use App\Services\PengajuanPerjalananService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanPerjalananController extends BaseApiController
{
    public function __construct(
        protected PengajuanPerjalananService $service
    ) {}

    /**
     * Display a listing of pengajuan perjalanan.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'id_karyawan', 'id_proyek', 'id_tujuan', 'search', 'paginate']);
        $perPage = (int) $request->get('per_page', 15);

        $result = $this->service->getAll($filters, $perPage);

        if ($result instanceof LengthAwarePaginator) {
            $data = PengajuanPerjalananResource::collection($result)->response()->getData(true);

            return $this->sendSuccess('Data pengajuan perjalanan berhasil diambil.', $data);
        }

        return $this->sendSuccess('Data pengajuan perjalanan berhasil diambil.', PengajuanPerjalananResource::collection($result));
    }

    /**
     * Store a newly created pengajuan perjalanan.
     */
    public function store(StorePengajuanPerjalananRequest $request): JsonResponse
    {
        try {
            $pengajuan = $this->service->create($request->validated());

            return $this->sendSuccess(
                'Pengajuan perjalanan tugas berhasil dibuat.',
                new PengajuanPerjalananResource($pengajuan),
                201
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 400);
        }
    }

    /**
     * Display the specified pengajuan perjalanan.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pengajuan = $this->service->getById($id);

            return $this->sendSuccess(
                'Detail pengajuan perjalanan berhasil diambil.',
                new PengajuanPerjalananResource($pengajuan)
            );
        } catch (Exception $e) {
            return $this->sendError('Data pengajuan perjalanan tidak ditemukan.', null, 404);
        }
    }

    /**
     * Update the specified pengajuan perjalanan.
     */
    public function update(UpdatePengajuanPerjalananRequest $request, int $id): JsonResponse
    {
        try {
            $pengajuan = $this->service->getById($id);
            $updated = $this->service->update($pengajuan, $request->validated());

            return $this->sendSuccess(
                'Pengajuan perjalanan tugas berhasil diperbarui.',
                new PengajuanPerjalananResource($updated)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Remove the specified pengajuan perjalanan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $pengajuan = $this->service->getById($id);
            $this->service->delete($pengajuan);

            return $this->sendSuccess('Pengajuan perjalanan tugas berhasil dihapus.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }

    /**
     * Update the status of pengajuan perjalanan.
     */
    public function updateStatus(UpdateStatusPengajuanRequest $request, int $id): JsonResponse
    {
        try {
            $pengajuan = $this->service->getById($id);
            $targetStatus = StatusPengajuanEnum::from($request->validated('status'));
            $updated = $this->service->updateStatus($pengajuan, $targetStatus, $request->validated('catatan'));

            return $this->sendSuccess(
                'Status pengajuan perjalanan berhasil diperbarui.',
                new PengajuanPerjalananResource($updated)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 422);
        }
    }
}
