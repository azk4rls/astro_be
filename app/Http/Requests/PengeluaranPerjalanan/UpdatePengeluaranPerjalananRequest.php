<?php

namespace App\Http\Requests\PengeluaranPerjalanan;

use App\Http\Requests\BaseFormRequest;

class UpdatePengeluaranPerjalananRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pengajuan' => ['nullable', 'integer'],
            'id_kategori' => ['sometimes', 'required', 'integer', 'exists:kategori_pengeluaran,id_kategori'],
            'tanggal_pengeluaran' => ['sometimes', 'required', 'date'],
            'deskripsi' => ['nullable', 'string'],
            'jumlah' => ['sometimes', 'required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['sometimes', 'required', 'string', 'max:50'],
            'sudah_diganti' => ['sometimes', 'boolean'],
        ];
    }
}
