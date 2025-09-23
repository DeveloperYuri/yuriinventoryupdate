<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SparepartinModel extends Model
{
    use HasFactory;

    protected $table = 'sparepartin';

    protected $fillable = [
        'name',
        'stock',
        'location',
        'user',
        'note',
    ];

    static public function getRecord($request)
    {
        $return = self::select('sparepartin.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('sparepartin.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
