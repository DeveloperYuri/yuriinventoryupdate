<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AssettoolsoutModel extends Model
{
    use HasFactory;

    protected $table = 'assettoolsout';

    protected $fillable = [
        'name',
        'brand',
        'stock',
        'location',
        'user_id',
        'note',
    ];

    static public function getRecord($request)
    {
        $return = self::select('assettoolsout.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('assettoolsout.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
