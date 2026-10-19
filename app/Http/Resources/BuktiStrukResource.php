<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BuktiStrukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fileUrl = $this->path_file ? Storage::disk('public')->url($this->path_file) : null;

        return [
            'id_bukti_struk' => $this->id_bukti_struk,
            'id_pengeluaran' => $this->id_pengeluaran,
            'nama_asli' => $this->nama_asli,
            'mime_type' => $this->mime_type,
            'ukuran' => $this->ukuran,
            'url' => $fileUrl,
            'uploaded_at' => $this->uploaded_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
