<?php

namespace App\Http\Requests\Karyawan;

use App\Http\Requests\BaseFormRequest;

class StoreKaryawanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:50', 'unique:karyawan,nik'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:karyawan,email'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'id_departemen' => ['required', 'integer', 'exists:departemen,id_departemen'],
            'peran' => ['required', 'string', 'max:100'],
        ];
    }
}
