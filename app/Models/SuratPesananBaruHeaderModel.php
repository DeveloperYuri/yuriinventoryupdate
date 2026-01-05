<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


class SuratPesananBaruHeaderModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_baru_header';

    protected $fillable = [
        'no_surat_pesanan',
        'name',
        'department_id',
        'locations_id',
        'category_id',
        'subcategory_id',
        'status'
    ];

    static public function getRecord($request)
    {
        $return = self::select('surat_pesanan_baru_header.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('surat_pesanan_baru_header.name', 'like', '%' . Request::get('name') . '%');
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

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    public function details()
    {
        return $this->hasMany(SuratPesananBaruDetailModel::class, 'surat_pesanan_baru_header_id', 'id');
    }

}
