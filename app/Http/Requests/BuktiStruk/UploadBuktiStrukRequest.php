<?php

namespace App\Http\Requests\BuktiStruk;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UploadBuktiStrukRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_pengeluaran' => 'required|integer|exists:pengeluaran_perjalanan,id_pengeluaran',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'file' => 'berkas bukti struk',
            'id_pengeluaran' => 'ID pengeluaran',
        ];
    }
}
