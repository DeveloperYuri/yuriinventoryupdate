<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtktransactionModel extends Model
{

    use HasFactory;

    protected $table = 'atk_transaction';

    protected $fillable = ['atk_id', 'type', 'quantity', 'user', 'atk_masuk_header_id', 'atk_keluar_header_id'];

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
}
