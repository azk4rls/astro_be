<?php

namespace App\Models;

use App\Enums\SettlementStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    use HasFactory;

    protected $table = 'settlement';

    protected $primaryKey = 'id_settlement';

    protected $attributes = [
        'status' => 'diajukan',
    ];

    protected $fillable = [
        'id_pengajuan',
        'status',
        'total_realisasi',
    ];

    protected $casts = [
        'status' => SettlementStatusEnum::class,
        'total_realisasi' => 'decimal:2',
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
