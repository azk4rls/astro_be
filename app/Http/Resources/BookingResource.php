<?php

namespace App\Http\Resources;

use App\Enums\BookingStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof BookingStatusEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof BookingStatusEnum ? $this->status->label() : $this->status;

        return [
            'id_booking' => $this->id_booking,
            'id_pengajuan' => $this->id_pengajuan,
            'nomor_pengajuan' => $this->whenLoaded('pengajuan', fn () => $this->pengajuan?->nomor_pengajuan),
            'jenis' => $this->jenis,
            'detail' => $this->detail,
            'nomor_ref' => $this->nomor_ref,
            'vendor' => $this->vendor,
            'harga' => (float) $this->harga,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
