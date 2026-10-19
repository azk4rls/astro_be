<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Pengguna\StorePenggunaRequest;
use App\Http\Requests\Pengguna\UpdatePenggunaRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->sendSuccess(
            'Daftar pengguna berhasil diambil.',
            UserResource::collection(User::with('departemen')->orderBy('name')->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenggunaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->sendSuccess('Pengguna berhasil ditambahkan.', new UserResource($user->load('departemen')), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $user = User::with('departemen')->find($id);

        if (! $user) {
            return $this->sendError('Pengguna tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail pengguna berhasil diambil.', new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenggunaRequest $request, string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->sendError('Pengguna tidak ditemukan.', null, 404);
        }

        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $this->sendSuccess('Pengguna berhasil diperbarui.', new UserResource($user->load('departemen')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return $this->sendError('Pengguna tidak ditemukan.', null, 404);
        }

        $user->delete();

        return $this->sendSuccess('Pengguna berhasil dihapus.', null);
    }
}
