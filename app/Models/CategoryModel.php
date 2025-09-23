<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class CategoryModel extends Model
{
    use HasFactory;

    protected $table = "category";

    protected $fillable = [
        'name'
    ];

    static public function getRecord($request)
    {
        $return = self::select('category.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('category.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategoryModel::class, 'category_id');
    }
}
