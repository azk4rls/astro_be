<?php

namespace App\Http\Resources;

use App\Enums\KondisiPenggantianEnum;
use App\Enums\StatusPenggantianEnum;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanPenggantianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof StatusPenggantianEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof StatusPenggantianEnum ? $this->status->label() : $this->status;

        $kondisiValue = $this->kondisi_penggantian instanceof KondisiPenggantianEnum ? $this->kondisi_penggantian->value : $this->kondisi_penggantian;
        $kondisiLabel = $this->kondisi_penggantian instanceof KondisiPenggantianEnum ? $this->kondisi_penggantian->label() : $this->kondisi_penggantian;

        return [
            'id_penggantian' => $this->id_penggantian,
            'nomor_penggantian' => $this->nomor_penggantian,
            'id_pengajuan' => $this->id_pengajuan,
            'pengajuan_perjalanan' => new PengajuanPerjalananResource($this->whenLoaded('pengajuanPerjalanan')),
            'tanggal_pengajuan' => $this->tanggal_pengajuan instanceof CarbonInterface
                ? $this->tanggal_pengajuan->format('Y-m-d')
                : $this->tanggal_pengajuan,
            'total_pengeluaran' => (float) $this->total_pengeluaran,
            'uang_muka' => (float) $this->uang_muka,
            'selisih' => (float) $this->selisih,
            'kondisi_penggantian' => $kondisiValue,
            'kondisi_penggantian_label' => $kondisiLabel,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'catatan' => $this->catatan,
            'details' => DetailPengajuanPenggantianResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
