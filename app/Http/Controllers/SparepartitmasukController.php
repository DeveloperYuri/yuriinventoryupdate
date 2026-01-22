<?php

namespace App\Http\Controllers;

use App\Models\SparepartitmasukModel;
use App\Models\SparepartitModel;
use App\Models\SparepartittrasactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparepartitmasukController extends Controller
{
    public function index(Request $request)
    {
        $query = SparepartitmasukModel::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.sparepartit.sparepartitmasuk.index', compact('transactions'));
    }

    public function create()
    {
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = SparepartitmasukModel::whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "IT/IN/{$tahun}/{$nextNumber}";

        return view('dashboard.sparepartit.sparepartitmasuk.create', compact('noDokumen'));
    }

    public function store(Request $request)
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
            $header = SparepartitmasukModel::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diterima_dari' => $request->diterima_dari,
                'diterima_oleh' => $request->diterima_oleh,
                'status' => 'sukses',
            ]);

            foreach ($request->product as $i => $sparepartit_id) {
                $atk  = SparepartitModel::findOrFail($sparepartit_id);

                SparepartittrasactionModel::create([
                    'sparepartit_masuk_header_id' => $header->id,
                    'sparepartit_id'               => $sparepartit_id,
                    'type'               => 'in',
                    'quantity'           => $request->demand[$i],
                    'user'               => $request->diterima_oleh,
                    'status' => 'sukses',
                ]);
            }

            return redirect()->route('sparepartitinmultiple.index')->with('success', 'Stok masuk berhasil dicatat.');
        });
    }

    public function show($id)
    {
        $transaction = SparepartitmasukModel::with('stockTransactions.sparePart')->findOrFail($id);

        return view('dashboard.sparepartit.sparepartitmasuk.show', compact('transaction'));
    }
}
