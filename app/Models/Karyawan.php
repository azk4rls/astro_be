<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'email',
        'telepon',
        'id_departemen',
        'peran',
    ];

    /**
     * Relasi ke model Departemen (Many-to-One).
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    /**
     * Relasi ke PengajuanPerjalananTugas (One-to-Many).
     */
    public function pengajuanPerjalanan(): HasMany
    {
        return $this->hasMany(PengajuanPerjalananTugas::class, 'id_karyawan', 'id_karyawan');
    }
}
