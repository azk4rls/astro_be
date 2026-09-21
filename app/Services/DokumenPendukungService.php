<?php

namespace App\Services;

use App\Models\DokumenPendukung;
use App\Models\PengajuanPerjalananTugas;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DokumenPendukungService
{
    /**
     * Upload berkas dokumen pendukung dan simpan record ke DB.
     *
     * @throws Exception
     */
    public function upload(UploadedFile $file, int $idPengajuan, string $jenis): DokumenPendukung
    {
        $pengajuan = PengajuanPerjalananTugas::find($idPengajuan);

        if (! $pengajuan) {
            throw new Exception("Data pengajuan perjalanan dengan ID {$idPengajuan} tidak ditemukan.");
        }

        $folder = 'dokumen/'.date('Y/m');
        $path = $file->store($folder, 'public');

        if (! $path) {
            throw new Exception('Gagal menyimpan berkas dokumen pendukung ke storage.');
        }

        return DokumenPendukung::create([
            'id_pengajuan' => $idPengajuan,
            'path_file' => $path,
            'nama_asli' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'ukuran' => $file->getSize(),
            'jenis_dokumen' => $jenis,
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Hapus berkas fisik dan record dokumen pendukung dari DB.
     *
     * @throws Exception
     */
    public function delete(int $idDokumen): bool
    {
        $dokumen = DokumenPendukung::find($idDokumen);

        if (! $dokumen) {
            throw new Exception("Dokumen pendukung dengan ID {$idDokumen} tidak ditemukan.");
        }

        if ($dokumen->path_file && Storage::disk('public')->exists($dokumen->path_file)) {
            Storage::disk('public')->delete($dokumen->path_file);
        }

        return (bool) $dokumen->delete();
    }

    /**
     * Ambil daftar dokumen pendukung berdasarkan ID Pengajuan Perjalanan.
     *
     * @throws Exception
     */
    public function getByPengajuan(int $idPengajuan): Collection
    {
        $pengajuan = PengajuanPerjalananTugas::find($idPengajuan);

        if (! $pengajuan) {
            throw new Exception("Data pengajuan perjalanan dengan ID {$idPengajuan} tidak ditemukan.");
        }

        return DokumenPendukung::where('id_pengajuan', $idPengajuan)->get();
    }
}
