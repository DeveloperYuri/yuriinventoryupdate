<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPesananDetailModel extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_detail';

    protected $fillable = [
        'surat_pesanan_header_id',
        'spare_part_id',
        'qty',
        'stock',
    ];

    public function header()
    {
        return $this->belongsTo(SuratPesananHeaderModel::class, 'surat_pesanan_header_id', 'id');
    }

    public function sparePart()
    {
        return $this->belongsTo(ListSparePartModel::class, 'spare_part_id', 'id');
    }
}
