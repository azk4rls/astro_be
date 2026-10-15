<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Settlement\StoreSettlementRequest;
use App\Http\Requests\Settlement\UpdateSettlementRequest;
use App\Http\Resources\SettlementResource;
use App\Models\Settlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettlementController extends BaseApiController
{
    /**
     * Display a listing of the resource, optionally filtered by trip.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Settlement::with('pengajuan')->orderByDesc('id_settlement');

        if ($request->filled('id_pengajuan')) {
            $query->where('id_pengajuan', $request->integer('id_pengajuan'));
        }

        return $this->sendSuccess(
            'Daftar settlement berhasil diambil.',
            SettlementResource::collection($query->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettlementRequest $request): JsonResponse
    {
        $settlement = Settlement::create($request->validated());

        return $this->sendSuccess(
            'Settlement berhasil diajukan.',
            new SettlementResource($settlement->load('pengajuan')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $settlement = Settlement::with('pengajuan')->find($id);

        if (! $settlement) {
            return $this->sendError('Settlement tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail settlement berhasil diambil.', new SettlementResource($settlement));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettlementRequest $request, string|int $id): JsonResponse
    {
        $settlement = Settlement::find($id);

        if (! $settlement) {
            return $this->sendError('Settlement tidak ditemukan.', null, 404);
        }

        $settlement->update($request->validated());

        return $this->sendSuccess(
            'Settlement berhasil diperbarui.',
            new SettlementResource($settlement->load('pengajuan'))
        );
    }

    /**
     * Update settlement status.
     */
    public function updateStatus(Request $request, string|int $id): JsonResponse
    {
        $settlement = Settlement::find($id);

        if (! $settlement) {
            return $this->sendError('Settlement tidak ditemukan.', null, 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['diajukan', 'diverifikasi', 'selesai'])],
        ]);

        $settlement->update(['status' => $validated['status']]);

        return $this->sendSuccess(
            'Status settlement berhasil diperbarui.',
            new SettlementResource($settlement->load('pengajuan'))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $settlement = Settlement::find($id);

        if (! $settlement) {
            return $this->sendError('Settlement tidak ditemukan.', null, 404);
        }

        $settlement->delete();

        return $this->sendSuccess('Settlement berhasil dihapus.', null);
    }
}
