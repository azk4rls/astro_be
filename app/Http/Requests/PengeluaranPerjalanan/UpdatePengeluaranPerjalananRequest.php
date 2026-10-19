<?php

namespace App\Http\Requests\PengeluaranPerjalanan;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdatePengeluaranPerjalananRequest extends BaseFormRequest
{
    /**
     * Drop an explicit null so the existing value is kept
     * and validated() never carries a null value.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('sudah_diganti') === null) {
            $this->offsetUnset('sudah_diganti');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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
