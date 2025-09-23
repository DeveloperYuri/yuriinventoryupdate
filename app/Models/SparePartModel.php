<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePartModel extends Model
{
    use HasFactory;

    protected $table = 'sparepart';

    protected $fillable = [
        'image',
        'description',
        'brand_id',
        'price',
        'stock',
        'location',
        'status',
    ];

}
