<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DokumenPendukungResource extends JsonResource
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
            'id_dokumen' => $this->id_dokumen,
            'id_pengajuan' => $this->id_pengajuan,
            'nama_asli' => $this->nama_asli,
            'mime_type' => $this->mime_type,
            'ukuran' => $this->ukuran,
            'jenis_dokumen' => $this->jenis_dokumen,
            'url' => $fileUrl,
            'uploaded_at' => $this->uploaded_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
