<?php

namespace App\Models;

use App\Enums\ActionPersetujuanEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RiwayatPersetujuan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_persetujuan';

    protected $primaryKey = 'id_persetujuan';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'id_user',
        'nama_approver',
        'peran_approver',
        'status',
        'catatan',
        'approved_at',
    ];

    protected $casts = [
        'status' => ActionPersetujuanEnum::class,
        'approved_at' => 'datetime',
    ];

    /**
     * Polymorphic relation ke dokumen yang di-approve (pengajuan_perjalanan / pengajuan_penggantian).
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke User (approver).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
