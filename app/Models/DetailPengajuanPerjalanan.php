<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengajuanPerjalanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pengajuan_perjalanan';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pengajuan',
        'id_kategori',
        'estimasi_biaya',
        'keterangan',
    ];

    protected $casts = [
        'estimasi_biaya' => 'decimal:2',
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke KategoriPengeluaran (Many-to-One).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori', 'id_kategori');
    }
}
