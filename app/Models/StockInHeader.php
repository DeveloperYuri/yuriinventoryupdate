<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'po_numbers',
        'status',
        'keterangan'
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

     public function getTanggalDisplayAttribute()
    {
        return $this->tanggal
            ? Carbon::parse($this->tanggal)->translatedFormat('d F Y')
            : '-';
    }
}
