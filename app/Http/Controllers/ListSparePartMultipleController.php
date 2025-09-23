<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ListSparePartModel;
use App\Models\LocationsModel;
use App\Models\StockInHeader;
use App\Models\StockOutHeader;
use App\Models\StockTransactionModel;
use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ListSparePartMultipleController extends Controller
{

    // Spare Part In
    public function index(Request $request)
    {
        $query = StockInHeader::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.sparepartinmultiple.listsparepartinmultiple', compact('transactions'));
    }

    public function create()
    {
        // $noDokumen = 'WH43/IN/' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = StockInHeader::whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "WH/IN/{$tahun}/{$nextNumber}";

        $suppliers = SupplierModel::all();


        return view('dashboard.sparepartinmultiple.create', compact('noDokumen', 'suppliers'));
    }

    public function storein(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'diterima_dari'  => 'required|string|max:100',
            'diterima_oleh'  => 'required|string|max:100',
        ], [
            'diterima_dari' => 'Form ini harus diisi',
            'diterima_oleh' => 'Form ini harus diisi',
        ]);

        return DB::transaction(function () use ($request) {
            $header = StockInHeader::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diterima_dari' => $request->diterima_dari,
                'diterima_oleh' => $request->diterima_oleh,
                'supplier_id' => $request->supplier_id,
                'po_numbers' => $request->po_numbers,
                // 'user'          => auth()->user()->name,
            ]);

            foreach ($request->product as $i => $spare_part_id) {
                $sparePart = ListSparePartModel::findOrFail($spare_part_id);

                StockTransactionModel::create([
                    'stock_in_header_id' => $header->id,
                    'spare_part_id'      => $spare_part_id,
                    'type'               => 'in',
                    'quantity'           => $request->demand[$i],
                    'price'              => $sparePart->price, // harga snapshot dari master
                    'user'               => $request->diterima_oleh,
                    // 'user'               => auth()->user()->name,
                ]);
            }

            return redirect()->route('sparepartinmultiple.index')->with('success', 'Stok masuk berhasil dicatat.');
        });
    }

    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([], 200);
        }

        $results = ListSparePartModel::whereNotNull('name')
            ->where('name', '!=', '')
            ->where('name', 'like', '%' . $q . '%')
            ->limit(20)
            ->get();

        $formatted = $results->map(function ($item) {
            return [
                'label' => $item->name,
                'value' => $item->name,
                'id'    => $item->id,   // tambahkan id
            ];
        });


        return response()->json($formatted);
    }

    public function show($id)
    {
        $transaction = StockInHeader::with('stockTransactions.sparePart')->findOrFail($id);

        return view('dashboard.sparepartinmultiple.show', compact('transaction'));
    }

    // Spare Part Out
    public function indexout(Request $request)
    {
        $query = StockOutHeader::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.sparepartoutmultiple.listsparepartoutmultiple', compact('transactions'));
    }

    public function createout()
    {
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = StockOutHeader::whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "WH/OUT/{$tahun}/{$nextNumber}";

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();

        return view('dashboard.sparepartoutmultiple.create', compact('noDokumen', 'locations', 'categories'));
    }

    public function storeout(Request $request)
    {
        // Validasi input dasar dulu
        $request->validate([
            'diminta_oleh'  => 'required|string|max:100',
            'product'       => 'required|array',
            'demand'        => 'required|array',
        ], [
            'diminta_oleh.required' => 'Form ini harus diisi',
        ]);

        $errors = [];

        // Loop cek stok semua produk
        foreach ($request->product as $i => $spare_part_id) {
            $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

            if (!$sparePart) {
                $errors["product.$i"] = "Spare part tidak ditemukan.";
                continue;
            }

            if ($request->demand[$i] > $sparePart->stock) {
                $errors["demand.$i"] = "Jumlah keluar melebihi stok yang tersedia (Stok: {$sparePart->stock}).";
            }
        }

        // Jika ada error stok, kembalikan dengan error sekaligus
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Jika valid, proses simpan transaksi stok keluar
        return DB::transaction(function () use ($request) {
            $header = StockOutHeader::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diminta_oleh'  => $request->diminta_oleh,
                'locations_id' => $request->locations_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id
            ]);

            foreach ($request->product as $i => $spare_part_id) {
                $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

                StockTransactionModel::create([
                    'stock_out_header_id' => $header->id,
                    'spare_part_id'       => $spare_part_id,
                    'type'                => 'out',
                    'quantity'            => $request->demand[$i],
                    'price'               => $sparePart->price, // ambil harga dari master
                    'user'                => $request->diminta_oleh
                ]);
            }

            return redirect()->route('sparepartoutmultiple.index')->with('success', 'Stok keluar berhasil dicatat.');
        });
    }

    // public function storeout(Request $request)
    // {

    //     // Validasi input
    //     $validated = $request->validate([
    //         'diminta_oleh'  => 'required|string|max:100',
    //     ], [
    //         'diminta_oleh' => 'Form ini harus diisi',
    //     ]);

    //     return DB::transaction(function () use ($request) {
    //         $header = StockOutHeader::create([
    //             'no_dokumen'    => $request->no_dokumen,
    //             'tanggal'       => $request->tanggal,
    //             'diminta_oleh' => $request->diminta_oleh
    //         ]);

    //         foreach ($request->product as $i => $spare_part_id) {
    //             StockTransactionModel::create([
    //                 'stock_out_header_id' => $header->id,
    //                 'spare_part_id'      => $spare_part_id,
    //                 'type'               => 'out',
    //                 'quantity'           => $request->demand[$i],
    //                 'user'               => $request->diminta_oleh
    //             ]);
    //         }

    //         return redirect()->route('sparepartoutmultiple.index')->with('success', 'Stok masuk berhasil dicatat.');
    //     });
    // }

    public function showout($id)
    {
        $transaction = StockOutHeader::with('stockTransactions.sparePart')->findOrFail($id);

        return view('dashboard.sparepartoutmultiple.show', compact('transaction'));
    }
}
