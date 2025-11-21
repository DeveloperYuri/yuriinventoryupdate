<?php

namespace App\Http\Controllers;

use App\Models\AtkkeluarModel;
use App\Models\AtkmasukModel;
use App\Models\AtkModel;
use App\Models\AtktransactionModel;
use App\Models\LocationsModel;
use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtkmasukController extends Controller
{
    public function index(Request $request)
    {
        $query = AtkmasukModel::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.atk.atkmasuk.index', compact('transactions'));
    }

    public function create()
    {
        // $noDokumen = 'WH43/IN/' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = AtkmasukModel::whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "ATK/IN/{$tahun}/{$nextNumber}";

        $suppliers = SupplierModel::all();

        return view('dashboard.atk.atkmasuk.create', compact('noDokumen', 'suppliers'));
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
            $header = AtkmasukModel::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diterima_dari' => $request->diterima_dari,
                'diterima_oleh' => $request->diterima_oleh,
                'supplier_id' => $request->supplier_id,
                'po_numbers' => $request->po_numbers,
                // 'user'          => auth()->user()->name,
            ]);

            foreach ($request->product as $i => $atk_id) {
                $atk  = AtkModel::findOrFail($atk_id);

                AtktransactionModel::create([
                    'atk_masuk_header_id' => $header->id,
                    'atk_id'               => $atk_id,
                    'type'               => 'in',
                    'quantity'           => $request->demand[$i],
                    // 'price'              => $sparePart->price, // harga snapshot dari master
                    'user'               => $request->diterima_oleh,
                    // 'user'               => auth()->user()->name,
                ]);
            }

            return redirect()->route('atkmasuk.index')->with('success', 'Stok masuk berhasil dicatat.');
        });
    }

    public function show($id)
    {
        $atktransaction = AtkmasukModel::with('stockTransactions.atk')->findOrFail($id);

        return view('dashboard.atk.atkmasuk.show', compact('atktransaction'));
    }

    public function createout()
    {
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = AtkkeluarModel::whereYear('tanggal', $tahun)
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

        return view('dashboard.sparepartoutmultiple.create', compact('noDokumen', 'locations', 'categories'));
    }
}
