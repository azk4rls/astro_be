<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPengajuanPerjalananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_detail' => $this->id_detail,
            'id_pengajuan' => $this->id_pengajuan,
            'id_kategori' => $this->id_kategori,
            'kategori' => new KategoriPengeluaranResource($this->whenLoaded('kategori')),
            'estimasi_biaya' => (float) $this->estimasi_biaya,
            'keterangan' => $this->keterangan,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
