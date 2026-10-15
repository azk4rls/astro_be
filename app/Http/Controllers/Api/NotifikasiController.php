<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Notifikasi\StoreNotifikasiRequest;
use App\Http\Requests\Notifikasi\UpdateNotifikasiRequest;
use App\Http\Resources\NotifikasiResource;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class NotifikasiController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->sendSuccess(
            'Daftar notifikasi berhasil diambil.',
            NotifikasiResource::collection(Notifikasi::orderByDesc('waktu')->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotifikasiRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['waktu'] = $data['waktu'] ?? Carbon::now();

        $notifikasi = Notifikasi::create($data);

        return $this->sendSuccess('Notifikasi berhasil ditambahkan.', new NotifikasiResource($notifikasi), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $notifikasi = Notifikasi::find($id);

        if (! $notifikasi) {
            return $this->sendError('Notifikasi tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail notifikasi berhasil diambil.', new NotifikasiResource($notifikasi));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotifikasiRequest $request, string|int $id): JsonResponse
    {
        $notifikasi = Notifikasi::find($id);

        if (! $notifikasi) {
            return $this->sendError('Notifikasi tidak ditemukan.', null, 404);
        }

        $notifikasi->update($request->validated());

        return $this->sendSuccess('Notifikasi berhasil diperbarui.', new NotifikasiResource($notifikasi));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $notifikasi = Notifikasi::find($id);

        if (! $notifikasi) {
            return $this->sendError('Notifikasi tidak ditemukan.', null, 404);
        }

        $notifikasi->delete();

        return $this->sendSuccess('Notifikasi berhasil dihapus.', null);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        Notifikasi::where('dibaca', false)->update(['dibaca' => true]);

        return $this->sendSuccess('Semua notifikasi ditandai sudah dibaca.', null);
    }
}
