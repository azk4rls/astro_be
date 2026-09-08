<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Departemen\StoreDepartemenRequest;
use App\Http\Requests\Departemen\UpdateDepartemenRequest;
use App\Http\Resources\DepartemenResource;
use App\Models\Departemen;
use Illuminate\Http\JsonResponse;

class DepartemenController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $departemen = Departemen::withCount('karyawan')->get();

        return $this->sendSuccess(
            'Daftar departemen berhasil diambil.',
            DepartemenResource::collection($departemen)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartemenRequest $request): JsonResponse
    {
        $departemen = Departemen::create($request->validated());

        return $this->sendSuccess(
            'Departemen berhasil ditambahkan.',
            new DepartemenResource($departemen),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $departemen = Departemen::with('karyawan')->find($id);

        if (!$departemen) {
            return $this->sendError('Departemen tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail departemen berhasil diambil.',
            new DepartemenResource($departemen)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartemenRequest $request, string|int $id): JsonResponse
    {
        $departemen = Departemen::find($id);

        if (!$departemen) {
            return $this->sendError('Departemen tidak ditemukan.', null, 404);
        }

        $departemen->update($request->validated());

        return $this->sendSuccess(
            'Departemen berhasil diperbarui.',
            new DepartemenResource($departemen)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $departemen = Departemen::find($id);

        if (!$departemen) {
            return $this->sendError('Departemen tidak ditemukan.', null, 404);
        }

        $departemen->delete();

        return $this->sendSuccess(
            'Departemen berhasil dihapus.',
            null
        );
    }
}
