<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_itinerary' => $this->id_itinerary,
            'id_pengajuan' => $this->id_pengajuan,
            'nomor_pengajuan' => $this->whenLoaded('pengajuan', fn () => $this->pengajuan?->nomor_pengajuan),
            'waktu' => $this->waktu?->toISOString(),
            'aktivitas' => $this->aktivitas,
            'lokasi' => $this->lokasi,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
