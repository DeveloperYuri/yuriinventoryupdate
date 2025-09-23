<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SubCategoryModel extends Model
{
    use HasFactory;

    protected $table = "subcategory";

    protected $fillable = [
        'name',
        'category_id'
    ];

    static public function getRecord($request)
    {
        $return = self::select('subcategory.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('subcategory.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}
