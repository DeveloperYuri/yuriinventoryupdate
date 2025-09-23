<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ListSparePartModel extends Model
{

    use HasFactory;

    protected $table = 'spare_parts';

    protected $fillable = ['name', 'stock', 'image', 'price', 'satuan', 'numbers'];
    
    public function transactions()
    {
        return $this->hasMany(StockTransactionModel::class);
    }

    static public function getRecord($request)
    {
        $return = self::select('spare_parts.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('spare_parts.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }

    static public function getRecordCard($request)
    {
        $return = self::select('spare_parts.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('spare_parts.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(9);
        return $return;
    }

}
