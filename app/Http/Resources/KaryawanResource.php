<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_karyawan' => $this->id_karyawan,
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'telepon' => $this->telepon,
            'id_departemen' => $this->id_departemen,
            'departemen' => new DepartemenResource($this->whenLoaded('departemen')),
            'peran' => $this->peran,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
