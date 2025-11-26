<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class RiwayatmesinModel extends Model
{
    use HasFactory;

    protected $table = 'riwayat_mesin';

    protected $fillable = [
        'tanggal',
        'nama_mesin',
        'running_hour',
        'pekerjaan',
        'pic',
        'deskripsi',
        'category_id',
        'subcategory_id',
        'status',
        'tanggal_selesai'
    ];

    static public function getRecord($request)
    {
        $return = self::select('riwayat_mesin.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('nama_mesin'))) {
                $return = $return->where('riwayat_mesin.nama_mesin', 'like', '%' . Request::get('nama_mesin') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategoryModel::class, 'subcategory_id');
    }
}
