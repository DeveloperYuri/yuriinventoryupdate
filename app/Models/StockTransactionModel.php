<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionModel extends Model
{

    use HasFactory;

    protected $table = 'stock_transactions';

    protected $fillable = ['spare_part_id', 'type', 'quantity', 'user', 'stock_in_header_id', 'stock_out_header_id', 'price'];

    public function sparePart()
    {
        return $this->belongsTo(ListSparePartModel::class);
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
}
