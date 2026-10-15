<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Delegasi\StoreDelegasiRequest;
use App\Http\Requests\Delegasi\UpdateDelegasiRequest;
use App\Http\Resources\DelegasiResource;
use App\Models\Delegasi;
use Illuminate\Http\JsonResponse;

class DelegasiController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->sendSuccess(
            'Daftar delegasi berhasil diambil.',
            DelegasiResource::collection(
                Delegasi::with(['pemberi', 'penerima'])->orderByDesc('dari_tgl')->get()
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDelegasiRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['id_pemberi'] = $data['id_pemberi'] ?? $request->user()?->id;

        $delegasi = Delegasi::create($data);

        return $this->sendSuccess(
            'Delegasi berhasil disimpan.',
            new DelegasiResource($delegasi->load(['pemberi', 'penerima'])),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $delegasi = Delegasi::with(['pemberi', 'penerima'])->find($id);

        if (! $delegasi) {
            return $this->sendError('Delegasi tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail delegasi berhasil diambil.', new DelegasiResource($delegasi));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDelegasiRequest $request, string|int $id): JsonResponse
    {
        $delegasi = Delegasi::find($id);

        if (! $delegasi) {
            return $this->sendError('Delegasi tidak ditemukan.', null, 404);
        }

        $delegasi->update($request->validated());

        return $this->sendSuccess(
            'Delegasi berhasil diperbarui.',
            new DelegasiResource($delegasi->load(['pemberi', 'penerima']))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $delegasi = Delegasi::find($id);

        if (! $delegasi) {
            return $this->sendError('Delegasi tidak ditemukan.', null, 404);
        }

        $delegasi->delete();

        return $this->sendSuccess('Delegasi berhasil dihapus.', null);
    }
}
