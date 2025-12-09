<?php

namespace App\Http\Controllers;

use App\Models\AtkkeluarModel;
use App\Models\AtktransactionModel;
use App\Models\LocationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AtkkeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = AtkkeluarModel::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.atk.atkkeluar.index', compact('transactions'));
    }

    public function create()
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
        $noDokumen = "ATK/OUT/{$tahun}/{$nextNumber}";

        $locations = LocationsModel::all();

        return view('dashboard.atk.atkkeluar.create', compact('noDokumen', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'diminta_oleh'  => 'required|string|max:100',
            'locations_id'   => 'required|exists:locations,id',
            'product'       => 'required|array|min:1',
            'product.*'     => 'required|exists:atk,id',
            'demand'        => 'required|array',
            'demand.*'      => 'required|integer|min:1',
        ], [
            'diminta_oleh.required' => 'form ini harus di isi',
            'locations_id.required' => 'Lokasi wajib dipilih',
            'locations_id.exists'   => 'Lokasi tidak valid',
        ]);

        return DB::transaction(function () use ($request) {

            // 🔑 1. AGREGASI TOTAL QTY PER ATK
            $totalDemand = [];
            foreach ($request->product as $i => $atk_id) {
                $qty = (int) $request->demand[$i];
                $totalDemand[$atk_id] = ($totalDemand[$atk_id] ?? 0) + $qty;
            }

            // 🔑 2. CEK STOK & KUMPULKAN ERROR
            $errors = [];
            foreach ($totalDemand as $atk_id => $sumQty) {
                $atk = \App\Models\AtkModel::lockForUpdate()->find($atk_id);

                if (!$atk) {
                    $errors['alert'][] = "ATK ID {$atk_id} tidak ditemukan";
                    continue;
                }

                if ($atk->stock <= 0) {
                    $errors['alert'][] = "Stok ATK {$atk->name} KOSONG";
                }

                if ($sumQty > $atk->stock) {
                    $errors['alert'][] =
                        "Stok ATK {$atk->name} tidak mencukupi (sisa {$atk->stock}, diminta {$sumQty})";
                }
            }

            // 🔴 JIKA ADA ERROR → STOP SEMUA
            if (!empty($errors)) {
                throw ValidationException::withMessages([
                    'alert' => $errors['alert']
                ]);
            }

            // ✅ 3. SIMPAN HEADER
            $header = AtkkeluarModel::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diminta_oleh'  => $request->diminta_oleh,
                'locations_id'  => $request->locations_id,
            ]);

            // ✅ 4. SIMPAN TRANSAKSI + KURANGI STOK
            foreach ($request->product as $i => $atk_id) {
                $qty = (int) $request->demand[$i];

                AtktransactionModel::create([
                    'atk_keluar_header_id' => $header->id,
                    'atk_id'               => $atk_id,
                    'type'                 => 'out',
                    'quantity'             => $qty,
                    'user'                 => $request->diminta_oleh,
                ]);
            }

            return redirect()->route('atk-keluar.index')
                ->with('success', 'ATK keluar berhasil dicatat.');
        });
    }

    // public function store(Request $request)
    // {

    //     // dd($request->all());

    //     // Validasi input dasar dulu
    //     // $request->validate([
    //     //     'diminta_oleh'  => 'required|string|max:100',
    //     //     'product'       => 'required|array',
    //     //     'demand'        => 'required|array',
    //     // ], [
    //     //     'diminta_oleh.required' => 'Form ini harus diisi',
    //     // ]);

    //     // $errors = [];

    //     // // Loop cek stok semua produk
    //     // foreach ($request->product as $i => $spare_part_id) {
    //     //     $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

    //     //     if (!$sparePart) {
    //     //         $errors["product.$i"] = "Spare part tidak ditemukan.";
    //     //         continue;
    //     //     }

    //     //     if ($request->demand[$i] > $sparePart->stock) {
    //     //         $errors["demand.$i"] = "Jumlah keluar melebihi stok yang tersedia (Stok: {$sparePart->stock}).";
    //     //     }
    //     // }

    //     // // Jika ada error stok, kembalikan dengan error sekaligus
    //     // if (!empty($errors)) {
    //     //     return back()->withErrors($errors)->withInput();
    //     // }

    //     // Jika valid, proses simpan transaksi stok keluar
    //     return DB::transaction(function () use ($request) {
    //         $header = AtkkeluarModel::create([
    //             'no_dokumen'    => $request->no_dokumen,
    //             'tanggal'       => $request->tanggal,
    //             'diminta_oleh'  => $request->diminta_oleh,
    //             'locations_id' => $request->locations_id
    //         ]);

    //         foreach ($request->product as $i => $atk_id) {
    //             $atk = \App\Models\AtkModel::find($atk_id);

    //             AtktransactionModel::create([
    //                 'atk_keluar_header_id' => $header->id,
    //                 'atk_id'              => $atk_id,
    //                 'type'                => 'out',
    //                 'quantity'            => $request->demand[$i],
    //                 'user'                => $request->diminta_oleh
    //             ]);
    //         }

    //         return redirect()->route('atk-keluar.index')->with('success', 'Atk keluar berhasil dicatat.');
    //     });
    // }

    public function show($id)
    {
        $atktransaction = AtkkeluarModel::with('stockTransactions.atk')->findOrFail($id);

        return view('dashboard.atk.atkkeluar.show', compact('atktransaction'));
    }
}
