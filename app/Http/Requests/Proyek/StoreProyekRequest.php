<?php

namespace App\Http\Requests\Proyek;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreProyekRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_proyek' => ['required', 'string', 'max:150'],
            'kode_proyek' => ['required', 'string', 'max:50', 'unique:proyek,kode_proyek'],
        ];
    }
}
