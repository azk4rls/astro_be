<?php

namespace App\Http\Requests\Persetujuan;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ApprovePersetujuanRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'approvable_type' => 'required|string|in:pengajuan_perjalanan,pengajuan_penggantian',
            'approvable_id' => 'required|integer|min:1',
            'nama_approver' => 'nullable|string|max:150',
            'peran_approver' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'approvable_type' => 'tipe dokumen',
            'approvable_id' => 'ID dokumen',
            'nama_approver' => 'nama approver',
            'peran_approver' => 'peran approver',
            'catatan' => 'catatan persetujuan',
        ];
    }
}
