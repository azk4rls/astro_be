<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PengeluaranPerjalanan\StorePengeluaranPerjalananRequest;
use App\Http\Requests\PengeluaranPerjalanan\UpdatePengeluaranPerjalananRequest;
use App\Http\Resources\PengeluaranPerjalananResource;
use App\Models\PengeluaranPerjalanan;
use Illuminate\Http\JsonResponse;

class PengeluaranPerjalananController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pengeluaran = PengeluaranPerjalanan::with('kategori')->get();

        return $this->sendSuccess(
            'Daftar pengeluaran perjalanan berhasil diambil.',
            PengeluaranPerjalananResource::collection($pengeluaran)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengeluaranPerjalananRequest $request): JsonResponse
    {
        $pengeluaran = PengeluaranPerjalanan::create($request->validated());
        $pengeluaran->load('kategori');

        return $this->sendSuccess(
            'Pengeluaran perjalanan berhasil ditambahkan.',
            new PengeluaranPerjalananResource($pengeluaran),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $pengeluaran = PengeluaranPerjalanan::with('kategori')->find($id);

        if (!$pengeluaran) {
            return $this->sendError('Pengeluaran perjalanan tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail pengeluaran perjalanan berhasil diambil.',
            new PengeluaranPerjalananResource($pengeluaran)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengeluaranPerjalananRequest $request, string|int $id): JsonResponse
    {
        $pengeluaran = PengeluaranPerjalanan::find($id);

        if (!$pengeluaran) {
            return $this->sendError('Pengeluaran perjalanan tidak ditemukan.', null, 404);
        }

        $pengeluaran->update($request->validated());
        $pengeluaran->load('kategori');

        return $this->sendSuccess(
            'Pengeluaran perjalanan berhasil diperbarui.',
            new PengeluaranPerjalananResource($pengeluaran)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $pengeluaran = PengeluaranPerjalanan::find($id);

        if (!$pengeluaran) {
            return $this->sendError('Pengeluaran perjalanan tidak ditemukan.', null, 404);
        }

        $pengeluaran->delete();

        return $this->sendSuccess(
            'Pengeluaran perjalanan berhasil dihapus.',
            null
        );
    }
}
