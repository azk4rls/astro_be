<?php

namespace App\Http\Requests\TravelPolicy;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreTravelPolicyRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latarbelakang' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'string', 'max:100'],
            'limit_biaya' => ['required', 'numeric', 'min:0'],
            'kelas' => ['required', 'string', 'max:100'],
        ];
    }
}
