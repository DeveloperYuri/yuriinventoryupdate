<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


class WarehouseModel extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    protected $fillable = [
        'name',
        'address',
        'pic'
    ];

    static public function getRecord($request)
    {
        $return = self::select('warehouse.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('warehouse.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
