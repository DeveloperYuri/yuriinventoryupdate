<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;


class BrandModel extends Model
{
    use HasFactory;

    protected $table = 'brand';

    protected $fillable = [
        'name'
    ];

    static public function getRecord($request)
    {
        $return = self::select('brand.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('brand.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
