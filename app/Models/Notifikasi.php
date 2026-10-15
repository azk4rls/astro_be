<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user',
        'judul',
        'isi',
        'waktu',
        'dibaca',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'dibaca' => 'boolean',
    ];

    /**
     * Relasi ke User (Many-to-One), opsional.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
