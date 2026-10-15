<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pengajuan' => ['sometimes', 'required', 'integer', 'exists:pengajuan_perjalanan_tugas,id_pengajuan'],
            'jenis' => ['sometimes', 'required', 'string', Rule::in(['Tiket Pesawat', 'Kereta', 'Hotel', 'Transportasi'])],
            'detail' => ['sometimes', 'required', 'string', 'max:200'],
            'nomor_ref' => ['nullable', 'string', 'max:100'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'harga' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];
    }
}
