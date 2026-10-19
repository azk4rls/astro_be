<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengajuanPenggantian extends Model
{
    use HasFactory;

    protected $table = 'detail_pengajuan_penggantian';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_penggantian',
        'id_kategori',
        'tanggal_pengeluaran',
        'deskripsi',
        'jumlah',
        'bukti_file',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date:Y-m-d',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Relasi ke PengajuanPenggantian (Many-to-One).
     */
    public function penggantian(): BelongsTo
    {
        return $this->belongsTo(PengajuanPenggantian::class, 'id_penggantian', 'id_penggantian');
    }

    /**
     * Relasi ke KategoriPengeluaran (Many-to-One).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori', 'id_kategori');
    }
}
