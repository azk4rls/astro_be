<?php

namespace App\Http\Requests\Notifikasi;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreNotifikasiRequest extends BaseFormRequest
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
            'judul' => ['required', 'string', 'max:150'],
            'isi' => ['required', 'string'],
            'waktu' => ['nullable', 'date'],
            'dibaca' => ['nullable', 'boolean'],
        ];
    }
}
