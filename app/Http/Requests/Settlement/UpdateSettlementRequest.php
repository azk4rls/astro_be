<?php

namespace App\Http\Requests\Settlement;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateSettlementRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pengajuan' => ['sometimes', 'required', 'integer', 'exists:pengajuan_perjalanan_tugas,id_pengajuan'],
            'status' => ['nullable', Rule::in(['diajukan', 'diverifikasi', 'selesai'])],
            'total_realisasi' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
