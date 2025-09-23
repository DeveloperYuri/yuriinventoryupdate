<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssettoolsModel extends Model
{
    use HasFactory;

    protected $table = 'assettools';

    protected $fillable = [
        'image',
        'name',
        'brand',
        'price',
        'stock',
        'location',
        'status',
        'note'
    ];
}
