<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermintaanSparePartDetailModel extends Model
{
    use HasFactory;

    protected $table = 'surat_permintaan_spare_part_detail';

    protected $fillable = [
        'surat_permintaan_header_id',
        'spare_part_id',
        'qty',
        'stock',
        'keterangan'
    ];

    public function header()
    {
        return $this->belongsTo(SuratPermintaanSparePartHeaderModel::class, 'surat_pesanan_header_id', 'id');
    }

    public function sparePart()
    {
        return $this->belongsTo(ListSparePartModel::class, 'spare_part_id', 'id');
    }
}
