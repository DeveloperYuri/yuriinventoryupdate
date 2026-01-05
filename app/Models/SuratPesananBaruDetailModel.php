<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPesananBaruDetailModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_baru_detail';

    protected $fillable = [
        'surat_pesanan_baru_header_id',
        'item_type',
        'item_id',
        'qty',
        'qty_kurang',
        'stock',
        'keterangan'
    ];

    public function item()
    {
        return $this->belongsTo(
            ItemAllModel::class,
            'item_id', // FK di tabel detail
            'id'       // PK di item_all
        );
    }
}
