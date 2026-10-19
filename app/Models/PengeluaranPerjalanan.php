<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengeluaranPerjalanan extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_perjalanan';

    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'id_pengajuan',
        'id_kategori',
        'tanggal_pengeluaran',
        'deskripsi',
        'jumlah',
        'metode_pembayaran',
        'sudah_diganti',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date:Y-m-d',
        'jumlah' => 'decimal:2',
        'sudah_diganti' => 'boolean',
    ];

    /**
     * Relasi ke model KategoriPengeluaran (Many-to-One).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi ke BuktiStruk (One-to-Many).
     */
    public function buktiStruk(): HasMany
    {
        return $this->hasMany(BuktiStruk::class, 'id_pengeluaran', 'id_pengeluaran');
    }
}
