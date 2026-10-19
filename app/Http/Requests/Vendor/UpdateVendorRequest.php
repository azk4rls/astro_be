<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateVendorRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_vendor' => ['sometimes', 'required', 'string', 'max:150'],
            'kategori' => ['sometimes', 'required', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:150'],
            'rating' => ['nullable', 'numeric', 'between:0,9.9'],
        ];
    }
}
