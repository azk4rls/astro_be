<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPendukung extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pendukung';

    protected $primaryKey = 'id_dokumen';

    protected $fillable = [
        'id_pengajuan',
        'path_file',
        'nama_asli',
        'mime_type',
        'ukuran',
        'jenis_dokumen',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'ukuran' => 'integer',
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
