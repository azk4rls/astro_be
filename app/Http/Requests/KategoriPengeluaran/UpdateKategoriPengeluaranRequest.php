<?php

namespace App\Http\Requests\KategoriPengeluaran;

use App\Http\Requests\BaseFormRequest;

class UpdateKategoriPengeluaranRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => ['sometimes', 'required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
