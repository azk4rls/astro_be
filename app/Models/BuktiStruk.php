<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiStruk extends Model
{
    use HasFactory;

    protected $table = 'bukti_struk';

    protected $primaryKey = 'id_bukti_struk';

    protected $fillable = [
        'id_pengeluaran',
        'path_file',
        'nama_asli',
        'mime_type',
        'ukuran',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'ukuran' => 'integer',
    ];

    /**
     * Relasi ke PengeluaranPerjalanan (Many-to-One).
     */
    public function pengeluaran(): BelongsTo
    {
        return $this->belongsTo(PengeluaranPerjalanan::class, 'id_pengeluaran', 'id_pengeluaran');
    }
}
