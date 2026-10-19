<?php

namespace App\Http\Requests\Delegasi;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreDelegasiRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pemberi' => ['nullable', 'integer', 'exists:users,id'],
            'id_penerima' => ['required', 'integer', 'exists:users,id'],
            'dari_tgl' => ['required', 'date'],
            'sampai_tgl' => ['required', 'date', 'after_or_equal:dari_tgl'],
            'alasan' => ['nullable', 'string', 'max:200'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }
}
