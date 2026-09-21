<?php

namespace App\Http\Requests\Proyek;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateProyekRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $proyekId = $this->route('proyek') ?? $this->route('id');
        if (is_object($proyekId)) {
            $proyekId = $proyekId->id_proyek;
        }

        return [
            'nama_proyek' => ['sometimes', 'required', 'string', 'max:150'],
            'kode_proyek' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('proyek', 'kode_proyek')->ignore($proyekId, 'id_proyek'),
            ],
        ];
    }
}
