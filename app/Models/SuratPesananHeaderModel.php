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
        'subcategory_id',
        'tanggal',
        'status_penerimaan',
        'keterangan',
        'ditujukan_kepada',
    ];

    static public function getRecord($request)
    {
        $return = self::select('surat_pesanan_header.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('no_surat_pesanan'))) {
            $return = $return->where('surat_pesanan_header.no_surat_pesanan', 'like', '%' . Request::get('no_surat_pesanan') . '%');
        }

        // Filter Ditujukan Kepada
        if (!empty($request->get('ditujukan_kepada'))) {
            $return = $return->where('ditujukan_kepada', '=', $request->get('ditujukan_kepada'));
        }

        // Filter Status
        if ($request->filled('status')) {
            $return->where('status', $request->status);
        }

        // Filter Status Penerimaan
        if ($request->filled('status_penerimaan')) {
            $return->where('status_penerimaan', $request->status_penerimaan);
        }

        $return = $return->paginate(10)->withQueryString();
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

    public function items()
    {
        // Gunakan class SuratPesananDetailModel dan foreign key surat_pesanan_header_id
        return $this->hasMany(SuratPesananDetailModel::class, 'surat_pesanan_header_id', 'id');
    }
}
