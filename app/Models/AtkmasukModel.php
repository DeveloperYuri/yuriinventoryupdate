<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkmasukModel extends Model
{
    use HasFactory;

    protected $table = 'atk_masuk_header';

    protected $fillable = [
        'no_dokumen',
        'diterima_dari',
        'diterima_oleh',
        'tanggal',
        'supplier_id',
        'sp_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(AtktransactionModel::class, 'atk_masuk_header_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class);
    }
}
