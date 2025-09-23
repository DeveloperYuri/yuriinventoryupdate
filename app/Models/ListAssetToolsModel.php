<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ListAssetToolsModel extends Model
{
    use HasFactory;

    protected $table = 'asset_tools';

    protected $fillable = ['name', 'stock', 'image', 'price', 'satuan'];
    
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
}
