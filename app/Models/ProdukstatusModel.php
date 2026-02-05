<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ProdukstatusModel extends Model
{
    use HasFactory;

    protected $table = 'produk_status';

    protected $fillable = [
        'name',
    ];

    static public function getRecord($request)
    {
        $return = self::select('produk_status.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('produk_status.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

}
