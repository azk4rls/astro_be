<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TravelPolicy\StoreTravelPolicyRequest;
use App\Http\Requests\TravelPolicy\UpdateTravelPolicyRequest;
use App\Http\Resources\TravelPolicyResource;
use App\Models\TravelPolicy;
use Illuminate\Http\JsonResponse;

class TravelPolicyController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->sendSuccess(
            'Daftar travel policy berhasil diambil.',
            TravelPolicyResource::collection(TravelPolicy::orderBy('id_policy')->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTravelPolicyRequest $request): JsonResponse
    {
        $policy = TravelPolicy::create($request->validated());

        return $this->sendSuccess('Travel policy berhasil ditambahkan.', new TravelPolicyResource($policy), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $policy = TravelPolicy::find($id);

        if (! $policy) {
            return $this->sendError('Travel policy tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail travel policy berhasil diambil.', new TravelPolicyResource($policy));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTravelPolicyRequest $request, string|int $id): JsonResponse
    {
        $policy = TravelPolicy::find($id);

        if (! $policy) {
            return $this->sendError('Travel policy tidak ditemukan.', null, 404);
        }

        $policy->update($request->validated());

        return $this->sendSuccess('Travel policy berhasil diperbarui.', new TravelPolicyResource($policy));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $policy = TravelPolicy::find($id);

        if (! $policy) {
            return $this->sendError('Travel policy tidak ditemukan.', null, 404);
        }

        $policy->delete();

        return $this->sendSuccess('Travel policy berhasil dihapus.', null);
    }
}
