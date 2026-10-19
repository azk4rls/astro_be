<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Karyawan\StoreKaryawanRequest;
use App\Http\Requests\Karyawan\UpdateKaryawanRequest;
use App\Http\Resources\KaryawanResource;
use App\Models\Karyawan;
use Illuminate\Http\JsonResponse;

class KaryawanController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $karyawan = Karyawan::with('departemen')->get();

        return $this->sendSuccess(
            'Daftar karyawan berhasil diambil.',
            KaryawanResource::collection($karyawan)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKaryawanRequest $request): JsonResponse
    {
        $karyawan = Karyawan::create($request->validated());
        $karyawan->load('departemen');

        return $this->sendSuccess(
            'Karyawan berhasil ditambahkan.',
            new KaryawanResource($karyawan),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $karyawan = Karyawan::with('departemen')->find($id);

        if (! $karyawan) {
            return $this->sendError('Karyawan tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess(
            'Detail karyawan berhasil diambil.',
            new KaryawanResource($karyawan)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKaryawanRequest $request, string|int $id): JsonResponse
    {
        $karyawan = Karyawan::find($id);

        if (! $karyawan) {
            return $this->sendError('Karyawan tidak ditemukan.', null, 404);
        }

        $karyawan->update($request->validated());
        $karyawan->load('departemen');

        return $this->sendSuccess(
            'Karyawan berhasil diperbarui.',
            new KaryawanResource($karyawan)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $karyawan = Karyawan::find($id);

        if (! $karyawan) {
            return $this->sendError('Karyawan tidak ditemukan.', null, 404);
        }

        $karyawan->delete();

        return $this->sendSuccess(
            'Karyawan berhasil dihapus.',
            null
        );
    }
}
