<?php

namespace App\Http\Requests\TravelPolicy;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateTravelPolicyRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latarbelakang' => ['sometimes', 'required', 'string', 'max:100'],
            'grade' => ['sometimes', 'required', 'string', 'max:100'],
            'limit_biaya' => ['sometimes', 'required', 'numeric', 'min:0'],
            'kelas' => ['sometimes', 'required', 'string', 'max:100'],
        ];
    }
}
