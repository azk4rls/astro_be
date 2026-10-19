<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPolicy extends Model
{
    use HasFactory;

    protected $table = 'travel_policy';

    protected $primaryKey = 'id_policy';

    protected $fillable = [
        'latarbelakang',
        'grade',
        'limit_biaya',
        'kelas',
    ];

    protected $casts = [
        'limit_biaya' => 'decimal:2',
    ];
}
