<?php

namespace App\Http\Requests\Itinerary;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreItineraryRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pengajuan' => ['required', 'integer', 'exists:pengajuan_perjalanan_tugas,id_pengajuan'],
            'waktu' => ['required', 'date'],
            'aktivitas' => ['required', 'string', 'max:200'],
            'lokasi' => ['required', 'string', 'max:200'],
        ];
    }
}
