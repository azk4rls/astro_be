<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_notifikasi' => $this->id_notifikasi,
            'id_user' => $this->id_user,
            'judul' => $this->judul,
            'isi' => $this->isi,
            'waktu' => $this->waktu?->toISOString(),
            'dibaca' => (bool) $this->dibaca,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
