<?php

namespace App\Http\Requests\PengajuanPerjalanan;

use App\Enums\StatusPengajuanEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusPengajuanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(StatusPengajuanEnum::class)],
            'catatan' => 'nullable|string',
        ];
    }
}
