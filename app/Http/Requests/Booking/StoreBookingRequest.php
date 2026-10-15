<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends BaseFormRequest
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
            'jenis' => ['required', 'string', Rule::in(['Tiket Pesawat', 'Kereta', 'Hotel', 'Transportasi'])],
            'detail' => ['required', 'string', 'max:200'],
            'nomor_ref' => ['nullable', 'string', 'max:100'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'harga' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];
    }
}
