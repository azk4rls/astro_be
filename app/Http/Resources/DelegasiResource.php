<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DelegasiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_delegasi' => $this->id_delegasi,
            'id_pemberi' => $this->id_pemberi,
            'pemberi' => $this->whenLoaded('pemberi', fn () => $this->pemberi?->name),
            'id_penerima' => $this->id_penerima,
            'penerima' => $this->whenLoaded('penerima', fn () => $this->penerima?->name),
            'dari_tgl' => $this->dari_tgl?->format('Y-m-d'),
            'sampai_tgl' => $this->sampai_tgl?->format('Y-m-d'),
            'alasan' => $this->alasan,
            'aktif' => (bool) $this->aktif,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
