<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class RiwayatpeminjamanassetitModel extends Model
{
    use HasFactory;

    protected $table = 'riwayat_peminjaman_asset_it';

    protected $fillable = [
        'nomer_asset',
        'nama',
        'user',
        'locations_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];

    static public function getRecord($request)
    {
        $return = self::select('riwayat_peminjaman_asset_it.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('nomer_asset'))) {
            $return = $return->where('riwayat_peminjaman_asset_it.nomer_asset', 'like', '%' . Request::get('nomer_asset') . '%');
        }

        $return = $return->paginate(2);
        return $return;
    }

    public function location()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }
}
