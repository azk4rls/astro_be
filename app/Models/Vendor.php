<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $primaryKey = 'id_vendor';

    protected $fillable = [
        'nama_vendor',
        'kategori',
        'kontak',
        'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];
}
