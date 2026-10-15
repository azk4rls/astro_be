<?php

namespace App\Models;

use App\Enums\FundStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UangMuka extends Model
{
    use HasFactory;

    protected $table = 'uang_muka';

    protected $primaryKey = 'id_uang_muka';

    protected $attributes = [
        'status' => 'diajukan',
        'disetujui' => 0.00,
    ];

    protected $fillable = [
        'id_pengajuan',
        'diajukan',
        'disetujui',
        'status',
        'tanggal',
    ];

    protected $casts = [
        'diajukan' => 'decimal:2',
        'disetujui' => 'decimal:2',
        'status' => FundStatusEnum::class,
        'tanggal' => 'date:Y-m-d',
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (Many-to-One).
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
