<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class DepartmentModel extends Model
{
    use HasFactory;

    protected $table = 'department';

    protected $fillable = [
        'name'
    ];

    static public function getRecord($request)
    {
        $return = self::select('department.*')->orderBy('id', 'desc');

        if (!empty(Request::get('name'))){
            $return = $return->where('department.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(10)->withQueryString();
        return $return;
    }
}
