<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ListSparePartModel extends Model
{

    use HasFactory;

    protected $table = 'spare_parts';

    protected $fillable = ['name', 'stock', 'image', 'price', 'satuan', 'numbers', 'category_id', 'subcategory_id'];

    public function transactions()
    {
        return $this->hasMany(StockTransactionModel::class);
    }

    static public function getRecord($request)
    {
        $query = self::with(['category', 'subcategory'])
            ->orderBy('id', 'desc');

        // 🔍 Search Nama
        if ($request->filled('name')) {
            $query->where('spare_parts.name', 'like', '%' . $request->name . '%');
        }

        // 📂 Filter Category
        if ($request->filled('category_id')) {
            $query->where('spare_parts.category_id', $request->category_id);
        }

        // 📁 Filter Sub Category
        if ($request->filled('subcategory_id')) {
            $query->where('spare_parts.subcategory_id', $request->subcategory_id);
        }

        return $query->paginate(10)->withQueryString();
    }


    // static public function getRecord($request)
    // {
    //     $return = self::select('spare_parts.*')
    //         //->where('status', '=', 'active')
    //         ->orderBy('id', 'desc');

    //     if (!empty(Request::get('name'))) {
    //         $return = $return->where('spare_parts.name', 'like', '%' . Request::get('name') . '%');
    //     }

    //     $return = $return->paginate(10)->withQueryString();;
    //     return $return;
    // }

    static public function getRecordCard($request)
    {
        $return = self::select('spare_parts.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('spare_parts.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(9);
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
