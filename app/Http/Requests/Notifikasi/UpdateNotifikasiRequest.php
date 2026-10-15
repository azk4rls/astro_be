<?php

namespace App\Http\Requests\Notifikasi;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateNotifikasiRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_user' => ['nullable', 'integer', 'exists:users,id'],
            'judul' => ['sometimes', 'required', 'string', 'max:150'],
            'isi' => ['sometimes', 'required', 'string'],
            'waktu' => ['nullable', 'date'],
            'dibaca' => ['nullable', 'boolean'],
        ];
    }
}
