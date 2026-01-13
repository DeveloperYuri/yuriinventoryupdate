<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionModel extends Model
{

    use HasFactory;

    protected $table = 'stock_transactions';

    protected $fillable = ['spare_part_id', 'type', 'quantity', 'user', 'stock_in_header_id', 'stock_out_header_id', 'price', 'status', 'keterangan'];

    // public function sparePart()
    // {
    //     return $this->belongsTo(ListSparePartModel::class);
    // }

    public function sparePart()
    {
        return $this->belongsTo(
            ListSparePartModel::class,
            'spare_part_id',
            'id'
        );
    }

    public function stockOutHeader()
    {
        return $this->belongsTo(StockOutHeader::class, 'stock_out_header_id');
    }

    public function stockInHeader()
    {
        return $this->belongsTo(StockInHeader::class, 'stock_in_header_id');
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $sparePart = $transaction->sparePart;
            if ($transaction->type == 'in') {
                $sparePart->increment('stock', $transaction->quantity);
            } else {
                $sparePart->decrement('stock', $transaction->quantity);
            }
        });
    }

    public function scopeEffective($query)
    {
        return $query->where('status', 'sukses');
    }

    public function isEffective()
    {
        return $this->status === 'sukses';
    }
}
