<?php

namespace App\Http\Requests\Tujuan;

use App\Http\Requests\BaseFormRequest;

class UpdateTujuanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_tujuan' => ['sometimes', 'required', 'string', 'max:150'],
            'kota' => ['sometimes', 'required', 'string', 'max:100'],
            'negara' => ['sometimes', 'required', 'string', 'max:100'],
        ];
    }
}
