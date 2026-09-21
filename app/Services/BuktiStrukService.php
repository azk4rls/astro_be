<?php

namespace App\Services;

use App\Models\BuktiStruk;
use App\Models\PengeluaranPerjalanan;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BuktiStrukService
{
    /**
     * Upload berkas bukti struk dan simpan record ke DB.
     *
     * @throws Exception
     */
    public function upload(UploadedFile $file, int $idPengeluaran): BuktiStruk
    {
        $pengeluaran = PengeluaranPerjalanan::find($idPengeluaran);

        if (! $pengeluaran) {
            throw new Exception("Data pengeluaran dengan ID {$idPengeluaran} tidak ditemukan.");
        }

        $folder = 'struk/'.date('Y/m');
        $path = $file->store($folder, 'public');

        if (! $path) {
            throw new Exception('Gagal menyimpan berkas bukti struk ke storage.');
        }

        return BuktiStruk::create([
            'id_pengeluaran' => $idPengeluaran,
            'path_file' => $path,
            'nama_asli' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'ukuran' => $file->getSize(),
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Hapus berkas fisik dan record bukti struk dari DB.
     *
     * @throws Exception
     */
    public function delete(int $idBuktiStruk): bool
    {
        $buktiStruk = BuktiStruk::find($idBuktiStruk);

        if (! $buktiStruk) {
            throw new Exception("Bukti struk dengan ID {$idBuktiStruk} tidak ditemukan.");
        }

        if ($buktiStruk->path_file && Storage::disk('public')->exists($buktiStruk->path_file)) {
            Storage::disk('public')->delete($buktiStruk->path_file);
        }

        return (bool) $buktiStruk->delete();
    }

    /**
     * Ambil daftar bukti struk berdasarkan ID Pengeluaran.
     *
     * @throws Exception
     */
    public function getByPengeluaran(int $idPengeluaran): Collection
    {
        $pengeluaran = PengeluaranPerjalanan::find($idPengeluaran);

        if (! $pengeluaran) {
            throw new Exception("Data pengeluaran dengan ID {$idPengeluaran} tidak ditemukan.");
        }

        return BuktiStruk::where('id_pengeluaran', $idPengeluaran)->get();
    }
}
