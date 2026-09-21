<?php

namespace App\Http\Resources;

use App\Enums\ActionPersetujuanEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatPersetujuanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof ActionPersetujuanEnum ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof ActionPersetujuanEnum ? $this->status->label() : $this->status;

        return [
            'id_persetujuan' => $this->id_persetujuan,
            'approvable_type' => $this->approvable_type,
            'approvable_id' => $this->approvable_id,
            'id_user' => $this->id_user,
            'nama_approver' => $this->nama_approver,
            'peran_approver' => $this->peran_approver,
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'catatan' => $this->catatan,
            'approved_at' => $this->approved_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
