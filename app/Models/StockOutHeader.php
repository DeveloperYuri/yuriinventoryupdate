<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutHeader extends Model
{
    use HasFactory;

    protected $table = 'stock_out_headers';

    protected $fillable = [
        'no_dokumen',
        'diminta_oleh',
        'tanggal',
        'locations_id',
        'category_id',
        'subcategory_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransactionModel::class);
    }

    public function location()
    {
        return $this->belongsTo(LocationsModel::class, 'locations_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategoryModel::class, 'subcategory_id');
    }
}
