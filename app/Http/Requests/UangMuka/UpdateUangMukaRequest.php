<?php

namespace App\Http\Requests\UangMuka;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateUangMukaRequest extends BaseFormRequest
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
            'diajukan' => ['sometimes', 'required', 'numeric', 'min:0'],
            'disetujui' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['diajukan', 'dicairkan'])],
            'tanggal' => ['sometimes', 'required', 'date'],
        ];
    }
}
