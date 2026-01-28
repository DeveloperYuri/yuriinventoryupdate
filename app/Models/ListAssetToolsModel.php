<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ListAssetToolsModel extends Model
{
    use HasFactory;

    protected $table = 'asset_tools';

    protected $fillable = ['name', 'stock', 'image', 'price', 'satuan', 'satuan_id'];

    public function transactions()
    {
        return $this->hasMany(StockAssetTransactionModel::class);
    }

    static public function getRecord($request)
    {
        $return = self::select('asset_tools.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('asset_tools.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

    static public function getRecordCard($request)
    {
        $return = self::select('asset_tools.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('asset_tools.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(9);
        return $return;
    }

    // static public function getRecordCard($request)
    // {
    //     $return = self::with([
    //         'latestStockTransaction.location'
    //     ])
    //         ->select('asset_tools.*')
    //         ->orderBy('id', 'desc');

    //     if (!empty($request->name)) {
    //         $return->where('asset_tools.name', 'like', '%' . $request->name . '%');
    //     }

    //     return $return->paginate(9);
    // }



    // static public function getRecordCard($request)
    // {
    //     $return = self::select('asset_tools.*')
    //         //->where('status', '=', 'active')
    //         ->orderBy('id', 'desc');

    //     if (!empty(Request::get('name'))) {
    //         $return = $return->where('asset_tools.name', 'like', '%' . Request::get('name') . '%');
    //     }

    //     $return = $return->paginate(9);
    //     return $return;
    // }

    public function stockTransactions()
    {
        return $this->hasMany(StockAssetTransactionModel::class, 'asset_tools_id');
    }

    public function latestStockTransaction()
    {
        return $this->hasOne(StockAssetTransactionModel::class, 'asset_tools_id')
            ->latestOfMany();
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanModel::class, 'satuan_id');
    }
}
