<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SatuanModel extends Model
{
    use HasFactory;

    protected $table = 'satuan';

    protected $fillable = [
        'name'
    ];

    static public function getRecord($request)
    {
        $return = self::select('satuan.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('satuan.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
