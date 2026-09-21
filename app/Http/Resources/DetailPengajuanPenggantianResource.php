<?php

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPengajuanPenggantianResource extends JsonResource
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
            'id_penggantian' => $this->id_penggantian,
            'id_kategori' => $this->id_kategori,
            'kategori' => new KategoriPengeluaranResource($this->whenLoaded('kategori')),
            'tanggal_pengeluaran' => $this->tanggal_pengeluaran instanceof CarbonInterface
                ? $this->tanggal_pengeluaran->format('Y-m-d')
                : $this->tanggal_pengeluaran,
            'deskripsi' => $this->deskripsi,
            'jumlah' => (float) $this->jumlah,
            'bukti_file' => $this->bukti_file,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
