<?php

namespace App\Http\Requests\PengajuanPenggantian;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePengajuanPenggantianRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pengajuan' => 'required|exists:pengajuan_perjalanan_tugas,id_pengajuan|unique:pengajuan_penggantian,id_pengajuan',
            'tanggal_pengajuan' => 'required|date',
            'catatan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_kategori' => 'required|exists:kategori_pengeluaran,id_kategori',
            'details.*.tanggal_pengeluaran' => 'required|date',
            'details.*.deskripsi' => 'nullable|string',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.bukti_file' => 'nullable|string',
        ];
    }
}
