<?php

namespace App\Http\Requests\PengajuanPenggantian;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdatePengajuanPenggantianRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_pengajuan' => 'sometimes|required|date',
            'catatan' => 'nullable|string',
            'details' => 'nullable|array',
            'details.*.id_kategori' => 'required|exists:kategori_pengeluaran,id_kategori',
            'details.*.tanggal_pengeluaran' => 'required|date',
            'details.*.deskripsi' => 'nullable|string',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.bukti_file' => 'nullable|string',
        ];
    }
}
