<?php

namespace App\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengeluaranPerjalananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_pengeluaran' => $this->id_pengeluaran,
            'id_pengajuan' => $this->id_pengajuan,
            'id_kategori' => $this->id_kategori,
            'kategori' => new KategoriPengeluaranResource($this->whenLoaded('kategori')),
            'tanggal_pengeluaran' => $this->tanggal_pengeluaran instanceof CarbonInterface
                ? $this->tanggal_pengeluaran->format('Y-m-d')
                : $this->tanggal_pengeluaran,
            'deskripsi' => $this->deskripsi,
            'jumlah' => (float) $this->jumlah,
            'metode_pembayaran' => $this->metode_pembayaran,
            'sudah_diganti' => (bool) $this->sudah_diganti,
            'bukti_struk' => BuktiStrukResource::collection($this->whenLoaded('buktiStruk')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
