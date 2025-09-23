<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SuratPesananHeaderModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_header';

    protected $fillable = [
        'no_surat_pesanan',
        'name',
        'locations_id',
        'category_id',
        'subcategory_id'
    ];

    static public function getRecord($request)
    {
        $return = self::select('surat_pesanan_header.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('surat_pesanan_header.name', 'like', '%' . Request::get('name') . '%');
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
        return $this->hasMany(SuratPesananDetailModel::class, 'surat_pesanan_header_id', 'id');
    }
}
