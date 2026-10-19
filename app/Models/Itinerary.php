<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itinerary';

    protected $primaryKey = 'id_itinerary';

    protected $fillable = [
        'id_pengajuan',
        'waktu',
        'aktivitas',
        'lokasi',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
