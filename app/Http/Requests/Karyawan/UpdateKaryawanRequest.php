<?php

namespace App\Http\Requests\Karyawan;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateKaryawanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $karyawanId = $this->route('karyawan') ?? $this->route('id');
        if (is_object($karyawanId)) {
            $karyawanId = $karyawanId->id_karyawan;
        }

        return [
            'nik' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('karyawan', 'nik')->ignore($karyawanId, 'id_karyawan'),
            ],
            'nama_lengkap' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('karyawan', 'email')->ignore($karyawanId, 'id_karyawan'),
            ],
            'telepon' => ['nullable', 'string', 'max:30'],
            'id_departemen' => ['sometimes', 'required', 'integer', 'exists:departemen,id_departemen'],
            'peran' => ['sometimes', 'required', 'string', 'max:100'],
        ];
    }
}
