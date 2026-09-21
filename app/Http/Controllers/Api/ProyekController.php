<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Proyek\StoreProyekRequest;
use App\Http\Requests\Proyek\UpdateProyekRequest;
use App\Http\Resources\ProyekResource;
use App\Models\Proyek;
use Illuminate\Http\JsonResponse;

class ProyekController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $proyek = Proyek::all();

        return $this->sendSuccess(
            'Daftar proyek berhasil diambil.',
            ProyekResource::collection($proyek)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProyekRequest $request): JsonResponse
    {
        $proyek = Proyek::create($request->validated());

        return $this->sendSuccess(
            'Proyek berhasil ditambahkan.',
            new ProyekResource($proyek),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $proyek = Proyek::find($id);

        if (! $proyek) {
            return $this->sendError('Proyek tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail proyek berhasil diambil.',
            new ProyekResource($proyek)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProyekRequest $request, string|int $id): JsonResponse
    {
        $proyek = Proyek::find($id);

        if (! $proyek) {
            return $this->sendError('Proyek tidak ditemukan.', null, 404);
        }

        $proyek->update($request->validated());

        return $this->sendSuccess(
            'Proyek berhasil diperbarui.',
            new ProyekResource($proyek)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $proyek = Proyek::find($id);

        if (! $proyek) {
            return $this->sendError('Proyek tidak ditemukan.', null, 404);
        }

        $proyek->delete();

        return $this->sendSuccess(
            'Proyek berhasil dihapus.',
            null
        );
    }
}
