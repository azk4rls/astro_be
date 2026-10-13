<?php

namespace App\Http\Requests\PengeluaranPerjalanan;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePengeluaranPerjalananRequest extends BaseFormRequest
{
    /**
     * Drop an explicit null so the database default applies
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
            'id_kategori' => ['required', 'integer', 'exists:kategori_pengeluaran,id_kategori'],
            'tanggal_pengeluaran' => ['required', 'date'],
            'deskripsi' => ['nullable', 'string'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'sudah_diganti' => ['nullable', 'boolean'],
        ];
    }
}
