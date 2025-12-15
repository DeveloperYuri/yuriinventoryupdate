<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


class SuratPermintaanSparePartHeaderModel extends Model
{
     use HasFactory;

    protected $table = 'surat_permintaan_spare_part_header';

    protected $fillable = [
        'no_surat_permintaan',
        'name',
        'locations_id',
        'category_id',
        'subcategory_id'
    ];

    static public function getRecord($request)
    {
        $return = self::select('surat_permintaan_spare_part_header.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('surat_permintaan_spare_part_header.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

    public function location()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategoryModel::class, 'subcategory_id');
    }

    public function details()
    {
        return $this->hasMany(SuratPermintaanSparePartDetailModel::class, 'surat_permintaan_header_id', 'id');
    }
}
