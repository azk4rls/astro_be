<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LogAuditResource;
use App\Services\LogAuditService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogAuditController extends BaseApiController
{
    public function __construct(
        protected LogAuditService $service
    ) {}

    /**
     * Display a listing of audit logs with optional filters and pagination.
     *
     * GET /api/log-audit
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['id_user', 'model_type', 'action']);
            $perPage = (int) $request->get('per_page', 15);

            $paginator = $this->service->getAll($filters, $perPage);
            $data = LogAuditResource::collection($paginator)->response()->getData(true);

            return $this->sendSuccess('Data log audit berhasil diambil.', $data);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 400);
        }
    }

    /**
     * Display audit history for a specific model instance.
     *
     * GET /api/log-audit/{modelType}/{modelId}
     */
    public function showHistory(string $modelType, int $modelId): JsonResponse
    {
        try {
            $history = $this->service->getHistory($modelType, $modelId);

            return $this->sendSuccess(
                'Riwayat audit model berhasil diambil.',
                LogAuditResource::collection($history)
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), null, 404);
        }
    }
}
