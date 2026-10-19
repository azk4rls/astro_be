<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UangMuka\StoreUangMukaRequest;
use App\Http\Requests\UangMuka\UpdateUangMukaRequest;
use App\Http\Resources\UangMukaResource;
use App\Models\UangMuka;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UangMukaController extends BaseApiController
{
    /**
     * Display a listing of the resource, optionally filtered by trip.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UangMuka::with('pengajuan')->orderByDesc('id_uang_muka');

        if ($request->filled('id_pengajuan')) {
            $query->where('id_pengajuan', $request->integer('id_pengajuan'));
        }

        return $this->sendSuccess(
            'Daftar uang muka berhasil diambil.',
            UangMukaResource::collection($query->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUangMukaRequest $request): JsonResponse
    {
        $uangMuka = UangMuka::create($request->validated());

        return $this->sendSuccess(
            'Uang muka berhasil diajukan.',
            new UangMukaResource($uangMuka->load('pengajuan')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $uangMuka = UangMuka::with('pengajuan')->find($id);

        if (! $uangMuka) {
            return $this->sendError('Uang muka tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail uang muka berhasil diambil.', new UangMukaResource($uangMuka));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUangMukaRequest $request, string|int $id): JsonResponse
    {
        $uangMuka = UangMuka::find($id);

        if (! $uangMuka) {
            return $this->sendError('Uang muka tidak ditemukan.', null, 404);
        }

        $uangMuka->update($request->validated());

        return $this->sendSuccess(
            'Uang muka berhasil diperbarui.',
            new UangMukaResource($uangMuka->load('pengajuan'))
        );
    }

    /**
     * Disburse the advance payment.
     */
    public function cairkan(string|int $id): JsonResponse
    {
        $uangMuka = UangMuka::find($id);

        if (! $uangMuka) {
            return $this->sendError('Uang muka tidak ditemukan.', null, 404);
        }

        $uangMuka->update([
            'status' => 'dicairkan',
            'disetujui' => $uangMuka->diajukan,
        ]);

        return $this->sendSuccess(
            'Uang muka berhasil dicairkan.',
            new UangMukaResource($uangMuka->load('pengajuan'))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $uangMuka = UangMuka::find($id);

        if (! $uangMuka) {
            return $this->sendError('Uang muka tidak ditemukan.', null, 404);
        }

        $uangMuka->delete();

        return $this->sendSuccess('Uang muka berhasil dihapus.', null);
    }
}
