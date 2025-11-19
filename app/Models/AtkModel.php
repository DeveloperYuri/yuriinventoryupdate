<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AtkModel extends Model
{
     use HasFactory;

    protected $table = 'atk';

    protected $fillable = [
        'name',
        'price',
        'image',
        'stock',
        'satuan_id'
    ];

    //  public function transactions()
    // {
    //     return $this->hasMany(StockTransactionModel::class);
    // }

    static public function getRecord($request)
    {
        $return = self::select('atk.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('atk.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }

    static public function getRecordCard($request)
    {
        $return = self::select('atk.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('atk.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(9);
        return $return;
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanModel::class, 'satuan_id');
    }

}
