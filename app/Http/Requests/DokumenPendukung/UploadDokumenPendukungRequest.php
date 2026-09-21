<?php

namespace App\Http\Requests\DokumenPendukung;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UploadDokumenPendukungRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'id_pengajuan' => 'required|integer|exists:pengajuan_perjalanan_tugas,id_pengajuan',
            'jenis_dokumen' => 'required|string|in:surat_tugas,itinerary,undangan,lainnya',
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
            'file' => 'berkas dokumen pendukung',
            'id_pengajuan' => 'ID pengajuan perjalanan',
            'jenis_dokumen' => 'jenis dokumen',
        ];
    }
}
