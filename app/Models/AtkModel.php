<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AtkModel extends Model
{
    use HasFactory;

    protected $table = 'atk';

    protected $fillable = [
        'name',
        'price',
        'image',
        'stock',
        'satuan_id',
        'category_id',
    ];

    public function transactions()
    {
        return $this->hasMany(
            AtktransactionModel::class,
            'atk_id', // foreign key di stock_transactions
            'id'             // primary key di spare_parts
        );
    }

    static public function getRecord($request)
    {
        $query = self::with(['category', 'satuan'])
            ->orderBy('id', 'desc');

        // Default Filter: Tampilkan Active jika user tidak sedang mencari status tertentu
        // if ($request->filled('produk_status_id')) {
        //     $query->where('produk_status_id', $request->produk_status_id);
        // } else {
        //     $query->whereHas('produkstatus', function ($q) {
        //         $q->where('name', 'Active');
        //     });
        // }

        // Search Nama
        if ($request->filled('name')) {
            // Pastikan nama tabel benar (atk, bukan spare_parts)
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Sub Category (Jika tabel ATK ada subcategory)
        // if ($request->filled('subcategory_id')) {
        //     $query->where('subcategory_id', $request->subcategory_id);
        // }

        // PENTING: Harus ada return di sini!
        return $query->paginate(10)->withQueryString();
    }


    // static public function getRecord($request)
    // {
    //     // $return = self::select('atk.*')
    //     //     //->where('status', '=', 'active')
    //     //     ->orderBy('id', 'desc');

    //     // if (!empty(Request::get('name'))) {
    //     //     $return = $return->where('atk.name', 'like', '%' . Request::get('name') . '%');
    //     // }

    //     // $return = $return->paginate(10);
    //     // return $return;

    //     $query = self::with(['category', 'satuan'])
    //         ->orderBy('id', 'desc');

    //     // Search Nama
    //     if ($request->filled('name')) {
    //         $query->where('spare_parts.name', 'like', '%' . $request->name . '%');
    //     }

    //     // Filter Category
    //     if ($request->filled('category_id')) {
    //         $query->where('spare_parts.category_id', $request->category_id);
    //     }

    //     // Filter Sub Category
    //     if ($request->filled('subcategory_id')) {
    //         $query->where('spare_parts.subcategory_id', $request->subcategory_id);
    //     }

    //     if ($request->filled('produk_status_id')) {
    //         $query->where('spare_parts.produk_status_id', $request->produk_status_id);
    //     }
    // }

    static public function getRecordCard($request)
    {
        $return = self::select('atk.*')
            //->where('status', '=', 'active')
            ->orderBy('id', 'desc');

        if (!empty(Request::get('name'))) {
            $return = $return->where('atk.name', 'like', '%' . Request::get('name') . '%');
        }

        $return = $return->paginate(9);
        return $return;
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanModel::class, 'satuan_id');
    }

    public function getTotalIn()
    {
        return $this->transactions()
            ->where('type', 'in')
            ->sum('quantity');
    }

    public function getTotalOut()
    {
        return $this->transactions()
            ->where('type', 'out')
            ->sum('quantity');
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}
