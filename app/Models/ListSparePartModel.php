<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ListSparePartModel extends Model
{

    use HasFactory;

    protected $table = 'spare_parts';

    protected $fillable = ['name', 'stock', 'image', 'price', 'satuan_lama', 'numbers', 'category_id', 'subcategory_id', 'satuan_id', 'produk_status_id'];

    // public function transactions()
    // {
    //     return $this->hasMany(StockTransactionModel::class);
    // }

    public function transactions()
    {
        return $this->hasMany(
            StockTransactionModel::class,
            'spare_part_id', // foreign key di stock_transactions
            'id'             // primary key di spare_parts
        );
    }
    
    static public function getRecord($request)
    {
        // $query = self::with(['category', 'subcategory', 'satuan'])
        //     ->orderBy('id', 'desc');

        $query = self::with(['category', 'subcategory', 'satuan', 'produkstatus'])
            ->orderBy('id', 'desc');

        // 1. Logika Filter Status
        if ($request->filled('produk_status_id')) {
            // Jika user memilih status tertentu di search, ikuti pilihan user
            $query->where('spare_parts.produk_status_id', $request->produk_status_id);
        } else {
            // DEFAULT: Jika user belum pilih status apa-apa, otomatis filter 'Active'
            $query->whereHas('produkstatus', function ($q) {
                $q->where('name', 'Active');
            });
        }

        // Search Nama
        if ($request->filled('name')) {
            $query->where('spare_parts.name', 'like', '%' . $request->name . '%');
        }

        // Filter Category
        if ($request->filled('category_id')) {
            $query->where('spare_parts.category_id', $request->category_id);
        }

        // Filter Sub Category
        if ($request->filled('subcategory_id')) {
            $query->where('spare_parts.subcategory_id', $request->subcategory_id);
        }

        if ($request->filled('produk_status_id')) {
            $query->where('spare_parts.produk_status_id', $request->produk_status_id);
        }

        return $query->paginate(10)->withQueryString();
    }

    static public function getRecordCard($request)
    {
        $return = self::select('spare_parts.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('spare_parts.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(9);
        return $return;
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategoryModel::class, 'subcategory_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanModel::class, 'satuan_id');
    }

    public function produkstatus()
    {
        return $this->belongsTo(ProdukstatusModel::class, 'produk_status_id');
    }

    // public function getTotalIn()
    // {
    //     return $this->transactions()
    //         ->where('type', 'in')
    //         ->sum('quantity');
    // }

    // public function getTotalOut()
    // {
    //     return $this->transactions()
    //         ->where('type', 'out')
    //         ->sum('quantity');
    // }

    // public function getInBefore($date)
    // {
    //     return $this->transactions()
    //         ->where('type', 'in')
    //         ->where('created_at', '<', $date)
    //         ->sum('quantity');
    // }

    // public function getOutBefore($date)
    // {
    //     return $this->transactions()
    //         ->where('type', 'out')
    //         ->where('created_at', '<', $date)
    //         ->sum('quantity');
    // }

    // public function getInPeriod($start, $end)
    // {
    //     return $this->transactions()
    //         ->where('type', 'in')
    //         ->whereBetween('created_at', [$start, $end])
    //         ->sum('quantity');
    // }

    // public function getOutPeriod($start, $end)
    // {
    //     return $this->transactions()
    //         ->where('type', 'out')
    //         ->whereBetween('created_at', [$start, $end])
    //         ->sum('quantity');
    // }

    // BARU V2
    public function getTotalIn()
    {
        return $this->transactions()
            ->where('type', 'in')
            ->where('status', 'sukses')
            ->sum('quantity');
    }

    public function getTotalOut()
    {
        return $this->transactions()
            ->where('type', 'out')
            ->where('status', 'sukses')
            ->sum('quantity');
    }

    public function getInBefore($date)
    {
        return $this->transactions()
            ->where('type', 'in')
            ->where('status', 'sukses')
            ->where('created_at', '<', $date)
            ->sum('quantity');
    }

    public function getOutBefore($date)
    {
        return $this->transactions()
            ->where('type', 'out')
            ->where('status', 'sukses')
            ->where('created_at', '<', $date)
            ->sum('quantity');
    }

    public function getInPeriod($start, $end)
    {
        return $this->transactions()
            ->where('type', 'in')
            ->where('status', 'sukses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('quantity');
    }

    public function getOutPeriod($start, $end)
    {
        return $this->transactions()
            ->where('type', 'out')
            ->where('status', 'sukses')
            ->whereBetween('created_at', [$start, $end])
            ->sum('quantity');
    }
}
