<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Tujuan\StoreTujuanRequest;
use App\Http\Requests\Tujuan\UpdateTujuanRequest;
use App\Http\Resources\TujuanResource;
use App\Models\Tujuan;
use Illuminate\Http\JsonResponse;

class TujuanController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tujuan = Tujuan::all();

        return $this->sendSuccess(
            'Daftar tujuan berhasil diambil.',
            TujuanResource::collection($tujuan)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTujuanRequest $request): JsonResponse
    {
        $tujuan = Tujuan::create($request->validated());

        return $this->sendSuccess(
            'Tujuan berhasil ditambahkan.',
            new TujuanResource($tujuan),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $tujuan = Tujuan::find($id);

        if (!$tujuan) {
            return $this->sendError('Tujuan tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail tujuan berhasil diambil.',
            new TujuanResource($tujuan)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTujuanRequest $request, string|int $id): JsonResponse
    {
        $tujuan = Tujuan::find($id);

        if (!$tujuan) {
            return $this->sendError('Tujuan tidak ditemukan.', null, 404);
        }

        $tujuan->update($request->validated());

        return $this->sendSuccess(
            'Tujuan berhasil diperbarui.',
            new TujuanResource($tujuan)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $tujuan = Tujuan::find($id);

        if (!$tujuan) {
            return $this->sendError('Tujuan tidak ditemukan.', null, 404);
        }

        $tujuan->delete();

        return $this->sendSuccess(
            'Tujuan berhasil dihapus.',
            null
        );
    }
}
