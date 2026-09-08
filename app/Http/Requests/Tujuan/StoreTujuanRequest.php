<?php

namespace App\Http\Requests\Tujuan;

use App\Http\Requests\BaseFormRequest;

class StoreTujuanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_tujuan' => ['required', 'string', 'max:150'],
            'kota' => ['required', 'string', 'max:100'],
            'negara' => ['required', 'string', 'max:100'],
        ];
    }
}
