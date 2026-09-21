<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengeluaran';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi ke model PengeluaranPerjalanan (One-to-Many).
     */
    public function pengeluaranPerjalanan(): HasMany
    {
        return $this->hasMany(PengeluaranPerjalanan::class, 'id_kategori', 'id_kategori');
    }
}
