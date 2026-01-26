<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


class RiwayatperbaikanassetitModel extends Model
{
    use HasFactory;

    protected $table = 'riwayat_perbaikan_asset_it';

    protected $fillable = [
        'image',
        'nomer_asset',
        'nama',
        'user',
        'locations_id',
        'kerusakan',
        'perbaikan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    static public function getRecord($request)
    {
        $return = self::select('riwayat_perbaikan_asset_it.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('nomer_asset'))) {
            $return = $return->where('riwayat_perbaikan_asset_it.nomer_asset', 'like', '%' . Request::get('nomer_asset') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

    public function location()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }

    public function spareparts()
    {
        return $this->hasMany(PerbaikansparepartModel::class, 'perbaikan_id');
    }
}
