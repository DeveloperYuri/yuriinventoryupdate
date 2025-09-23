<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInHeader extends Model
{
    use HasFactory;

    protected $table = 'stock_in_headers';

    protected $fillable = [
        'no_dokumen',
        'diterima_dari',
        'diterima_oleh',
        'tanggal',
        'supplier_id',
        'po_numbers'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransactionModel::class);
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class);
    }
}
