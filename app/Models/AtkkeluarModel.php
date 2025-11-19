<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkkeluarModel extends Model
{
    use HasFactory;

    protected $table = 'atk_keluar_header';

    protected $fillable = [
        'no_dokumen',
        'diminta_oleh',
        'tanggal',
        'locations_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(AtktransactionModel::class, 'atk_keluar_header_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class);
    }

    public function locations()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }
}
