<?php

namespace App\Models;

use App\Enums\BookingStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'id_pengajuan',
        'jenis',
        'detail',
        'nomor_ref',
        'vendor',
        'harga',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'status' => BookingStatusEnum::class,
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
