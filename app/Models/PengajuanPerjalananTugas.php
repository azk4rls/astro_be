<?php

namespace App\Models;

use App\Enums\StatusPengajuanEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PengajuanPerjalananTugas extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_perjalanan_tugas';

    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'nomor_pengajuan',
        'id_karyawan',
        'id_proyek',
        'id_tujuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'maksud_tujuan',
        'estimasi_biaya',
        'uang_muka',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'estimasi_biaya' => 'decimal:2',
        'uang_muka' => 'decimal:2',
        'status' => StatusPengajuanEnum::class,
    ];

    /**
     * Relasi ke Karyawan (Many-to-One).
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    /**
     * Relasi ke Proyek (Many-to-One, optional).
     */
    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    /**
     * Relasi ke Tujuan (Many-to-One).
     */
    public function tujuan(): BelongsTo
    {
        return $this->belongsTo(Tujuan::class, 'id_tujuan', 'id_tujuan');
    }

    /**
     * Relasi ke DetailPengajuanPerjalanan (One-to-Many).
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailPengajuanPerjalanan::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke PengeluaranPerjalanan (One-to-Many).
     */
    public function pengeluaran(): HasMany
    {
        return $this->hasMany(PengeluaranPerjalanan::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke PengajuanPenggantian (One-to-One).
     */
    public function penggantian(): HasOne
    {
        return $this->hasOne(PengajuanPenggantian::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke RiwayatPersetujuan (Polymorphic One-to-Many).
     */
    public function riwayatPersetujuan(): MorphMany
    {
        return $this->morphMany(RiwayatPersetujuan::class, 'approvable');
    }

    /**
     * Relasi ke DokumenPendukung (One-to-Many).
     */
    public function dokumenPendukung(): HasMany
    {
        return $this->hasMany(DokumenPendukung::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke Itinerary (One-to-Many).
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke Booking (One-to-Many).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke UangMuka (One-to-Many).
     */
    public function uangMuka(): HasMany
    {
        return $this->hasMany(UangMuka::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Relasi ke Settlement (One-to-Many).
     */
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'id_pengajuan', 'id_pengajuan');
    }
}
