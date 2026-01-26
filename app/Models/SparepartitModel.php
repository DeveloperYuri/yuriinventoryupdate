<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class SparepartitModel extends Model
{
    use HasFactory;

    protected $table = 'sparepart_it';

    protected $fillable = [
        'image',
        'name',
        'stock',
        'satuan_id',
    ];

    static public function getRecord($request)
    {
        $return = self::select('sparepart_it.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('sparepart_it.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(10);
        return $return;
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanModel::class, 'satuan_id');
    }

    public function transactions()
    {
        return $this->hasMany(
            SparepartittrasactionModel::class,
            'sparepartit_id', // foreign key di stock_transactions
            'id'             // primary key di spare_parts
        );
    }

    public function getTotalIn()
    {
        return $this->transactions()
            ->where('type', 'in')
            ->sum('quantity');
    }

    public function getTotalOut()
    {
        return $this->transactions()
            ->where('type', 'out')
            ->sum('quantity');
    }
}
