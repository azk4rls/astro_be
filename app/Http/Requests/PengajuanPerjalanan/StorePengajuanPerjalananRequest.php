<?php

namespace App\Http\Requests\PengajuanPerjalanan;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePengajuanPerjalananRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'id_proyek' => 'nullable|exists:proyek,id_proyek',
            'id_tujuan' => 'required|exists:tujuan,id_tujuan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'maksud_tujuan' => 'required|string',
            'uang_muka' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'details' => 'nullable|array',
            'details.*.id_kategori' => 'required|exists:kategori_pengeluaran,id_kategori',
            'details.*.estimasi_biaya' => 'required|numeric|min:0',
            'details.*.keterangan' => 'nullable|string',
        ];
    }
}
