<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerbaikansparepartModel extends Model
{
    use HasFactory;

    protected $table = 'perbaikan_sparepart';

    protected $fillable = [
        'perbaikan_id',
        'sparepartit_id',
        'qty',
    ];
}
