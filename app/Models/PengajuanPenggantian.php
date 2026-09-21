<?php

namespace App\Models;

use App\Enums\KondisiPenggantianEnum;
use App\Enums\StatusPenggantianEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PengajuanPenggantian extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_penggantian';

    protected $primaryKey = 'id_penggantian';

    protected $fillable = [
        'nomor_penggantian',
        'id_pengajuan',
        'tanggal_pengajuan',
        'total_pengeluaran',
        'uang_muka',
        'selisih',
        'kondisi_penggantian',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date:Y-m-d',
        'total_pengeluaran' => 'decimal:2',
        'uang_muka' => 'decimal:2',
        'selisih' => 'decimal:2',
        'kondisi_penggantian' => KondisiPenggantianEnum::class,
        'status' => StatusPenggantianEnum::class,
    ];

    /**
     * Relasi ke PengajuanPerjalananTugas (1-to-1).
     */
    public function pengajuanPerjalanan(): BelongsTo
    {
        return $this->belongsTo(PengajuanPerjalananTugas::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke DetailPengajuanPenggantian (One-to-Many).
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailPengajuanPenggantian::class, 'id_penggantian', 'id_penggantian');
    }

    /**
     * Relasi ke RiwayatPersetujuan (Polymorphic One-to-Many).
     */
    public function riwayatPersetujuan(): MorphMany
    {
        return $this->morphMany(RiwayatPersetujuan::class, 'approvable');
    }
}
