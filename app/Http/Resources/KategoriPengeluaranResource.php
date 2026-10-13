<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriPengeluaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_kategori' => $this->id_kategori,
            'nama_kategori' => $this->nama_kategori,
            'deskripsi' => $this->deskripsi,
            'pengeluaran_perjalanan_count' => $this->whenCounted('pengeluaranPerjalanan'),
            'pengeluaran_perjalanan' => PengeluaranPerjalananResource::collection($this->whenLoaded('pengeluaranPerjalanan')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
