<?php

namespace App\Http\Resources;

use App\Enums\StatusPengajuanEnum;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanPerjalananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof StatusPengajuanEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof StatusPengajuanEnum ? $this->status->label() : $this->status;

        return [
            'id_pengajuan' => $this->id_pengajuan,
            'nomor_pengajuan' => $this->nomor_pengajuan,
            'id_karyawan' => $this->id_karyawan,
            'karyawan' => new KaryawanResource($this->whenLoaded('karyawan')),
            'id_proyek' => $this->id_proyek,
            'proyek' => new ProyekResource($this->whenLoaded('proyek')),
            'id_tujuan' => $this->id_tujuan,
            'tujuan' => new TujuanResource($this->whenLoaded('tujuan')),
            'tanggal_mulai' => $this->tanggal_mulai instanceof CarbonInterface
                ? $this->tanggal_mulai->format('Y-m-d')
                : $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai instanceof CarbonInterface
                ? $this->tanggal_selesai->format('Y-m-d')
                : $this->tanggal_selesai,
            'maksud_tujuan' => $this->maksud_tujuan,
            'estimasi_biaya' => (float) $this->estimasi_biaya,
            'uang_muka' => (float) $this->uang_muka,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'catatan' => $this->catatan,
            'details' => DetailPengajuanPerjalananResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
