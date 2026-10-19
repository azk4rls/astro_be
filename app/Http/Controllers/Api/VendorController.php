<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class VendorController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->sendSuccess(
            'Daftar vendor berhasil diambil.',
            VendorResource::collection(Vendor::orderBy('nama_vendor')->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendorRequest $request): JsonResponse
    {
        $vendor = Vendor::create($request->validated());

        return $this->sendSuccess('Vendor berhasil ditambahkan.', new VendorResource($vendor), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (! $vendor) {
            return $this->sendError('Vendor tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail vendor berhasil diambil.', new VendorResource($vendor));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, string|int $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (! $vendor) {
            return $this->sendError('Vendor tidak ditemukan.', null, 404);
        }

        $vendor->update($request->validated());

        return $this->sendSuccess('Vendor berhasil diperbarui.', new VendorResource($vendor));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (! $vendor) {
            return $this->sendError('Vendor tidak ditemukan.', null, 404);
        }

        $vendor->delete();

        return $this->sendSuccess('Vendor berhasil dihapus.', null);
    }
}
