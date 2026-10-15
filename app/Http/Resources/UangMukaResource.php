<?php

namespace App\Http\Resources;

use App\Enums\FundStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UangMukaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof FundStatusEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof FundStatusEnum ? $this->status->label() : $this->status;

        return [
            'id_uang_muka' => $this->id_uang_muka,
            'id_pengajuan' => $this->id_pengajuan,
            'nomor_pengajuan' => $this->whenLoaded('pengajuan', fn () => $this->pengajuan?->nomor_pengajuan),
            'diajukan' => (float) $this->diajukan,
            'disetujui' => (float) $this->disetujui,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
