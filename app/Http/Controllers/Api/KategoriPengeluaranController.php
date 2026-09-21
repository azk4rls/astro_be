<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\KategoriPengeluaran\StoreKategoriPengeluaranRequest;
use App\Http\Requests\KategoriPengeluaran\UpdateKategoriPengeluaranRequest;
use App\Http\Resources\KategoriPengeluaranResource;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\JsonResponse;

class KategoriPengeluaranController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $kategori = KategoriPengeluaran::withCount('pengeluaranPerjalanan')->get();

        return $this->sendSuccess(
            'Daftar kategori pengeluaran berhasil diambil.',
            KategoriPengeluaranResource::collection($kategori)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKategoriPengeluaranRequest $request): JsonResponse
    {
        $kategori = KategoriPengeluaran::create($request->validated());

        return $this->sendSuccess(
            'Kategori pengeluaran berhasil ditambahkan.',
            new KategoriPengeluaranResource($kategori),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $kategori = KategoriPengeluaran::with('pengeluaranPerjalanan')->find($id);

        if (! $kategori) {
            return $this->sendError('Kategori pengeluaran tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail kategori pengeluaran berhasil diambil.',
            new KategoriPengeluaranResource($kategori)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKategoriPengeluaranRequest $request, string|int $id): JsonResponse
    {
        $kategori = KategoriPengeluaran::find($id);

        if (! $kategori) {
            return $this->sendError('Kategori pengeluaran tidak ditemukan.', null, 404);
        }

        $kategori->update($request->validated());

        return $this->sendSuccess(
            'Kategori pengeluaran berhasil diperbarui.',
            new KategoriPengeluaranResource($kategori)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $kategori = KategoriPengeluaran::find($id);

        if (! $kategori) {
            return $this->sendError('Kategori pengeluaran tidak ditemukan.', null, 404);
        }

        $kategori->delete();

        return $this->sendSuccess(
            'Kategori pengeluaran berhasil dihapus.',
            null
        );
    }
}
