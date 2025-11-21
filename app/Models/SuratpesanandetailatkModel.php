<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratpesanandetailatkModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_atk_detail';

    protected $fillable = [
        'surat_pesanan_atk_header_id',
        'atk_id',
        'qty',
        'stock'
    ];

    public function header()
    {
        return $this->belongsTo(SuratpesananatkheaderModel::class, 'surat_pesanan_atk_header_id', 'id');
    }

    public function atk()
    {
        return $this->belongsTo(AtkModel::class, 'atk_id', 'id');
    }
}
