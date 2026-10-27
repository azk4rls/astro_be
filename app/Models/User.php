<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'jabatan', 'nik', 'id_departemen'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @return BelongsTo<Departemen, $this>
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    /**
     * Relasi ke Notifikasi pengguna (One-to-Many).
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id');
    }

    /**
     * Relasi ke Delegasi yang diberikan (One-to-Many).
     */
    public function delegasiDiberikan(): HasMany
    {
        return $this->hasMany(Delegasi::class, 'id_pemberi', 'id');
    }

    /**
     * Relasi ke Delegasi yang diterima (One-to-Many).
     */
    public function delegasiDiterima(): HasMany
    {
        return $this->hasMany(Delegasi::class, 'id_penerima', 'id');
    }

    /**
     * Relasi ke Log Audit aktivitas user (One-to-Many).
     */
    public function logAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class, 'id_user', 'id');
    }

    /**
     * Relasi ke Riwayat Persetujuan oleh user (One-to-Many).
     */
    public function riwayatPersetujuan(): HasMany
    {
        return $this->hasMany(RiwayatPersetujuan::class, 'id_user', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
