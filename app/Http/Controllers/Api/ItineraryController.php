<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Itinerary\StoreItineraryRequest;
use App\Http\Requests\Itinerary\UpdateItineraryRequest;
use App\Http\Resources\ItineraryResource;
use App\Models\Itinerary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItineraryController extends BaseApiController
{
    /**
     * Display a listing of the resource, optionally filtered by trip.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Itinerary::with('pengajuan')->orderBy('waktu');

        if ($request->filled('id_pengajuan')) {
            $query->where('id_pengajuan', $request->integer('id_pengajuan'));
        }

        return $this->sendSuccess(
            'Daftar itinerary berhasil diambil.',
            ItineraryResource::collection($query->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItineraryRequest $request): JsonResponse
    {
        $itinerary = Itinerary::create($request->validated());

        return $this->sendSuccess(
            'Itinerary berhasil ditambahkan.',
            new ItineraryResource($itinerary->load('pengajuan')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $itinerary = Itinerary::with('pengajuan')->find($id);

        if (! $itinerary) {
            return $this->sendError('Itinerary tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail itinerary berhasil diambil.', new ItineraryResource($itinerary));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItineraryRequest $request, string|int $id): JsonResponse
    {
        $itinerary = Itinerary::find($id);

        if (! $itinerary) {
            return $this->sendError('Itinerary tidak ditemukan.', null, 404);
        }

        $itinerary->update($request->validated());

        return $this->sendSuccess(
            'Itinerary berhasil diperbarui.',
            new ItineraryResource($itinerary->load('pengajuan'))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $itinerary = Itinerary::find($id);

        if (! $itinerary) {
            return $this->sendError('Itinerary tidak ditemukan.', null, 404);
        }

        $itinerary->delete();

        return $this->sendSuccess('Itinerary berhasil dihapus.', null);
    }
}
