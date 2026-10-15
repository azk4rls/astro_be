<?php

namespace App\Http\Requests\Pengguna;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdatePenggunaRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('pengguna')),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', Rule::in(['karyawan', 'atasan', 'admin-travel', 'finance', 'superadmin'])],
            'jabatan' => ['nullable', 'string', 'max:120'],
            'nik' => ['nullable', 'string', 'max:50'],
            'id_departemen' => ['nullable', 'integer', 'exists:departemen,id_departemen'],
        ];
    }
}
