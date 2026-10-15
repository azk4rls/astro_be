<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delegasi extends Model
{
    use HasFactory;

    protected $table = 'delegasi';

    protected $primaryKey = 'id_delegasi';

    protected $fillable = [
        'id_pemberi',
        'id_penerima',
        'dari_tgl',
        'sampai_tgl',
        'alasan',
        'aktif',
    ];

    protected $casts = [
        'dari_tgl' => 'date:Y-m-d',
        'sampai_tgl' => 'date:Y-m-d',
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke User pemberi delegasi (Many-to-One).
     */
    public function pemberi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pemberi', 'id');
    }

    /**
     * Relasi ke User penerima delegasi (Many-to-One).
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_penerima', 'id');
    }
}
