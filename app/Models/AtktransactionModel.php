<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtktransactionModel extends Model
{

    use HasFactory;

    protected $table = 'atk_transaction';

    protected $fillable = ['atk_id', 'type', 'quantity', 'user', 'atk_masuk_header_id', 'atk_keluar_header_id', 'status', 'keterangan'];

    public function atk()
    {
        return $this->belongsTo(AtkModel::class);
    }

    public function atkKeluar()
    {
        return $this->belongsTo(AtkkeluarModel::class, 'atk_keluar_header_id');
    }

    public function atkMasuk()
    {
        return $this->belongsTo(AtkmasukModel::class, 'atk_masuk_header_id');
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $atk = $transaction->atk;
            if ($transaction->type == 'in') {
                $atk->increment('stock', $transaction->quantity);
            } else {
                $atk->decrement('stock', $transaction->quantity);
            }
        });
    }

    public function scopeEffective($query)
    {
        return $query->where('status', 'sukses');
    }

    public function isEffective()
    {
        return $this->status === 'sukses';
    }

    // public function getTanggalAttribute()
    // {
    //     if ($this->type == 'in' && $this->atkMasuk) {
    //         // Bungkus dengan \Carbon\Carbon::parse()
    //         return \Carbon\Carbon::parse($this->atkMasuk->tanggal);
    //     }

    //     // Cek jika tipe 'out' dan relasi ada
    //     if ($this->type == 'out' && $this->atkKeluar) {
    //         // Bungkus dengan \Carbon\Carbon::parse()
    //         return \Carbon\Carbon::parse($this->atkKeluar->tanggal);
    //     }

    //     return $this->created_at; // Fallback jika tidak ditemukan
    // }
}
