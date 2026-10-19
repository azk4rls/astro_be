<?php

namespace App\Http\Resources;

use App\Enums\SettlementStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof SettlementStatusEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof SettlementStatusEnum ? $this->status->label() : $this->status;

        return [
            'id_settlement' => $this->id_settlement,
            'id_pengajuan' => $this->id_pengajuan,
            'nomor_pengajuan' => $this->whenLoaded('pengajuan', fn () => $this->pengajuan?->nomor_pengajuan),
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'total_realisasi' => (float) $this->total_realisasi,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
