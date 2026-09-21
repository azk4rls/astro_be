<?php

namespace App\Http\Requests\PengajuanPenggantian;

use App\Enums\StatusPenggantianEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusPenggantianRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(StatusPenggantianEnum::class)],
            'catatan' => 'nullable|string',
        ];
    }
}
