<?php

namespace App\Http\Requests\Departemen;

use App\Http\Requests\BaseFormRequest;

class StoreDepartemenRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_departemen' => ['required', 'string', 'max:100'],
        ];
    }
}
