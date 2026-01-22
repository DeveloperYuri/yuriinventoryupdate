<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartittrasactionModel extends Model
{
    use HasFactory;

    protected $table = 'sparepartit_transaction';

    protected $fillable = [
        'sparepartit_id',
        'type',
        'quantity',
        'sparepartit_masuk_header_id',
        'user',
        'status',
        'keterangan'
    ];

    public function sparePart()
    {
        return $this->belongsTo(
            SparepartitModel::class, // ✅ MODEL YANG BENAR
            'sparepartit_id',        // FK di transaction
            'id'                     // PK di master
        );
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $sparePart = $transaction->sparePart;
            if ($transaction->type == 'in') {
                $sparePart->increment('stock', $transaction->quantity);
            } else {
                $sparePart->decrement('stock', $transaction->quantity);
            }
        });
    }
}
