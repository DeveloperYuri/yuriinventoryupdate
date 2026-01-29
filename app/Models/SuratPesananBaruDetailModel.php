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

    public function header()
    {
        return $this->belongsTo(SuratPesananHeaderModel::class, 'surat_pesanan_header_id', 'id');
    }

    public function sparePart()
    {
        // Hanya untuk item_type = 'sparepart'
        if ($this->item_type !== 'sparepart') return null;

        return ListSparePartModel::find($this->item_id);
    }

    // Tambahkan accessor supaya gampang di blade
    public function getSparePartNameAttribute()
    {
        return $this->sparePart()?->name ?? '-';
    }

    public function getItemNameAttribute()
    {
        switch ($this->item_type) {
            case 'sparepart':
                $item = ListSparePartModel::find($this->item_id);
                break;
            case 'asset':
                $item = ListAssetToolsModel::find($this->item_id);
                break;
            case 'atk':
                $item = ATKModel::find($this->item_id);
                break;
            default:
                $item = null;
        }

        return $item?->name ?? '-';
    }
    
    // public function sparePart()
    // {
    //     return $this->belongsTo(ListSparePartModel::class, 'spare_part_id', 'id');
    // }
}
