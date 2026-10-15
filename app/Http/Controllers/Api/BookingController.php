<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends BaseApiController
{
    /**
     * Display a listing of the resource, optionally filtered by trip.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with('pengajuan')->orderByDesc('id_booking');

        if ($request->filled('id_pengajuan')) {
            $query->where('id_pengajuan', $request->integer('id_pengajuan'));
        }

        return $this->sendSuccess(
            'Daftar booking berhasil diambil.',
            BookingResource::collection($query->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = Booking::create($request->validated());

        return $this->sendSuccess(
            'Booking berhasil dibuat.',
            new BookingResource($booking->load('pengajuan')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): JsonResponse
    {
        $booking = Booking::with('pengajuan')->find($id);

        if (! $booking) {
            return $this->sendError('Booking tidak ditemukan.', null, 404);
        }

        return $this->sendSuccess('Detail booking berhasil diambil.', new BookingResource($booking));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, string|int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (! $booking) {
            return $this->sendError('Booking tidak ditemukan.', null, 404);
        }

        $booking->update($request->validated());

        return $this->sendSuccess(
            'Booking berhasil diperbarui.',
            new BookingResource($booking->load('pengajuan'))
        );
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, string|int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (! $booking) {
            return $this->sendError('Booking tidak ditemukan.', null, 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ]);

        $booking->update(['status' => $validated['status']]);

        return $this->sendSuccess(
            'Status booking berhasil diperbarui.',
            new BookingResource($booking->load('pengajuan'))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (! $booking) {
            return $this->sendError('Booking tidak ditemukan.', null, 404);
        }

        $booking->delete();

        return $this->sendSuccess('Booking berhasil dihapus.', null);
    }
}
