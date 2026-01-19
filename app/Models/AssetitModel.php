<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AssetitModel extends Model
{
    use HasFactory;

    protected $table = 'asset_it';

    protected $fillable = [
        'image',
        'nomer_asset',
        'nama',
        'user',
        'locations_id',
        'spesifikasi',
        'status',
    ];

    static public function getRecord($request)
    {
        $return = self::select('asset_it.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('nama'))) {
            $return = $return->where('asset_it.nama', 'like', '%' . Request::get('nama') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

    public function location()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }
}
