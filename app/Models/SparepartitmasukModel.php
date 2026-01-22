<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartitmasukModel extends Model
{
    use HasFactory;

    protected $table = 'sparepartit_masuk_header';

    protected $fillable = [
        'no_dokumen',
        'diterima_dari',
        'diterima_oleh',
        'tanggal',
        'status',
        'keterangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(SparepartittrasactionModel::class, 'sparepartit_masuk_header_id');
    }
}
