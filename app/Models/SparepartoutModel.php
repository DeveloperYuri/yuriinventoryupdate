<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SparepartoutModel extends Model
{
    use HasFactory;

    protected $table = 'sparepartout';

    protected $fillable = [
        'name',
        'stock',
        'location',
        'user',
        'note',
    ];

    static public function getRecord($request)
    {
        $return = self::select('sparepartout.*')
            ->orderBy('id', 'desc');

            if (!empty(Request::get('name'))) {
                $return = $return->where('sparepartout.name', 'like', '%' . Request::get('name') . '%');
            }

        $return = $return->paginate(10);
        return $return;
    }
}
