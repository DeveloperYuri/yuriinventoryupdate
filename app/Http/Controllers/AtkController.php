<?php

namespace App\Http\Controllers;

use App\Exports\AtkExport;
use App\Exports\AtkHistoryExport;
use App\Models\AtkModel;
use App\Models\AtktransactionModel;
use App\Models\CategoryModel;
use App\Models\SatuanModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AtkController extends Controller
{
    // public function index(Request $request)
    // {
    //     $data['getRecord'] = AtkModel::getRecord($request);
    //     $categories = CategoryModel::all();

    //     return view('dashboard.atk.listatk', compact('data', 'categories'));
    // }

    public function index(Request $request)
    {
        // Langsung masukkan ke variabel sendiri
        $getRecord = AtkModel::getRecord($request);
        $categories = CategoryModel::all();

        // Kirimkan variabel secara terpisah
        return view('dashboard.atk.listatk', compact('getRecord', 'categories'));
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = AtkModel::getRecordCard($request);
        return view('dashboard.atk.cardlistatk', $data);
    }

    public function create()
    {
        $satuan = SatuanModel::all();
        $categories = CategoryModel::all();

        return view('dashboard.atk.create', compact('satuan', 'categories'));
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'satuan_id' => 'required|exists:satuan,id',
            // 'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'satuan_id.required' => 'Satuan wajib dipilih',


            // 'price.required' => 'Harga Spare Part wajib diisi',
            // 'price.integer' => 'Harga Spare Part harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $data = $request->only('name', 'price', 'satuan_id', 'stock', 'category_id');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        AtkModel::create($data);

        return redirect()->route('atk.index')->with('success', 'ATK berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $atk = AtkModel::findOrFail($id);
        $satuans = SatuanModel::all();
        $categories = CategoryModel::all();

        return view('dashboard.atk.edit', compact('atk', 'satuans', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            // 'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'category_id' => 'required|exists:category,id',
            'satuan_id' => 'required|exists:satuan,id',
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'satuan_id.required' => 'Satuan wajib dipilih',
            // 'price.required' => 'Harga Spare Part wajib diisi',
            // 'price.integer' => 'Harga Spare Part harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $atk = AtkModel::findOrFail($id);
        $atk->name = $request->name;
        $atk->price = $request->price;
        $atk->satuan_id = $request->satuan_id;
        $atk->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            if ($atk->image && file_exists(public_path('images/' . $atk->image))) {
                unlink(public_path('images/' . $atk->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $atk->image = $imageName;
        }

        $atk->save();

        return redirect()->route('atk.index')->with('success', 'ATK berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $atk = AtkModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($atk->image && file_exists(public_path('images/' . $atk->image))) {
            unlink(public_path('images/' . $atk->image));
        }

        $atk->delete();

        return redirect()->route('atk.index')->with('success', 'ATK berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([], 200);
        }

        $results = AtkModel::whereNotNull('name')
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

    public function history(Request $request)
    {
        $query = AtktransactionModel::with('atk')->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type); // in / out
        }

        $atktransactions = $query->paginate(20)->withQueryString();

        return view('dashboard.atk.history', compact('atktransactions'));
    }

    public function viewHistoryPerItem(Request $request, $id)
    {
        $sparePart = AtkModel::findOrFail($id);

        /* ===========================
     * QUERY UNTUK PERHITUNGAN
     * (HANYA TRANSAKSI SUKSES)
     * =========================== */
        $calcQuery = AtktransactionModel::effective()
            ->where('atk_id', $id);

        if ($request->start_date) {
            $calcQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $calcQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $calcTransactions = $calcQuery->orderBy('created_at')->get();

        $runningStock = 0;
        $runningValue = 0;

        foreach ($calcTransactions as $trx) {
            if ($trx->type === 'in') {
                $runningStock += $trx->quantity;
                $runningValue += $trx->quantity * $trx->price;
            } else {
                $runningStock -= $trx->quantity;
                $runningValue -= $trx->quantity * $trx->price;
            }

            $trx->runningStock = $runningStock;
            $trx->runningValue = $runningValue;
        }

        /* ===========================
     * QUERY UNTUK TAMPILAN
     * (TERMASUK BATAL)
     * =========================== */
        $transactions = AtktransactionModel::with(['atkKeluar.locations'])
            ->where('atk_id', $id)
            ->when(
                $request->start_date,
                fn($q) =>
                $q->whereDate('created_at', '>=', $request->start_date)
            )
            ->when(
                $request->end_date,
                fn($q) =>
                $q->whereDate('created_at', '<=', $request->end_date)
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // Ambil nilai akhir untuk footer
        $lastRunningValue = $calcTransactions->last()->runningValue ?? 0;
        $lastRunningStock = $runningStock;

        return view('dashboard.atk.detailatk', compact(
            'sparePart',
            'transactions',
            'lastRunningStock',
            'lastRunningValue'
        ));
    }


    // public function viewHistoryPerItem(Request $request, $id)
    // {
    //     $sparePart = AtkModel::findOrFail($id);

    //     $query = AtktransactionModel::with([
    //         'atkKeluar.locations'
    //     ])->where('atk_id', $id);

    //     if ($request->start_date) {
    //         $query->whereDate('created_at', '>=', $request->start_date);
    //     }

    //     if ($request->end_date) {
    //         $query->whereDate('created_at', '<=', $request->end_date);
    //     }

    //     $allTransactions = $query->orderBy('created_at')->get();

    //     $totalStock = 0;
    //     $runningValue = 0;

    //     // Hitung total stock dan total value
    //     $allTransactions->each(function ($item) use (&$totalStock, &$runningValue, $sparePart) {
    //         if ($item->type === 'in') {
    //             $totalStock += $item->quantity;
    //             $runningValue += $item->quantity * $item->price;
    //         } else {
    //             $totalStock -= $item->quantity;
    //             $runningValue -= $item->quantity * $item->price;
    //         }
    //         $item->runningStock = $totalStock; // simpan ke tiap item supaya bisa tampil di view
    //         $item->runningValue = $runningValue; // simpan total harga
    //     });

    //     $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

    //     return view('dashboard.atk.detailatk', compact('sparePart', 'transactions', 'totalStock', 'allTransactions'));
    // }

    public function cetakPDF()
    {
        $atk = AtkModel::all();
        $pdf = Pdf::loadView('dashboard.atk.laporanatkpdf', compact('atk'));
        return $pdf->download('laporan_sparepart.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new AtkExport, 'laporan_atk.xlsx');
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('term'); // jQuery UI pakai "term" sebagai key
        $data = AtkModel::where('name', 'LIKE', "%{$query}%")
            ->pluck('name'); // ambil hanya kolom name

        return response()->json($data);
    }


    public function exportHistoryPDF(Request $request)
    {
        $query = AtktransactionModel::with('atk')->orderBy('created_at', 'asc');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type); // in / out
        }

        $transactions = $query->get();

        $pdf = Pdf::loadView('dashboard.atk.atkpdf.atkhistory', compact('transactions'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_riwayat_atk.pdf');
    }

    public function exportHistoryExcel(Request $request)
    {
        return Excel::download(
            new AtkHistoryExport($request->start_date, $request->end_date, $request->type),
            'laporan_riwayat_sparepartinout.xlsx'
        );
    }

    public function batal(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string'
        ]);

        DB::transaction(function () use ($request, $id) {

            // 🔐 Lock transaksi
            $trx = AtktransactionModel::lockForUpdate()->findOrFail($id);

            if ($trx->status !== 'sukses') {
                abort(400, 'Transaksi sudah dibatalkan');
            }

            // 🔐 Lock stok ATK
            $atk = AtkModel::lockForUpdate()->findOrFail($trx->atk_id);

            if ($trx->type === 'in') {
                // ❗ BATAL MASUK → STOK DIKURANGI
                $atk->stock -= $trx->quantity;
            } else {
                // ❗ BATAL KELUAR → STOK DIKEMBALIKAN
                $atk->stock += $trx->quantity;
            }

            // 🛡 Cegah stok minus
            if ($atk->stock < 0) {
                abort(400, 'Stok menjadi negatif');
            }

            $atk->save();

            // Update transaksi
            $trx->update([
                'status' => 'batal',
                'keterangan' => $request->keterangan,
            ]);
        });

        return redirect()
            ->route('atk.history')
            ->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan');
    }


    // public function batal(Request $request, $id)
    // {
    //     $request->validate([
    //         'keterangan' => 'required|string'
    //     ]);

    //     DB::transaction(function () use ($request, $id) {

    //         $trx = AtktransactionModel::findOrFail($id);

    //         // Cegah double batal
    //         if ($trx->status !== 'sukses') {
    //             abort(400, 'Transaksi sudah dibatalkan');
    //         }

    //         // Rollback stok
    //         $atk = AtkModel::findOrFail($trx->atk_id);

    //         if ($trx->type === 'in') {
    //             // Masuk → stok dikurangi
    //             $atk->decrement('stock', $trx->quantity);
    //         } else {
    //             // Keluar → stok dikembalikan
    //             $atk->increment('stock', $trx->quantity);
    //         }

    //         // Update transaksi
    //         $trx->update([
    //             'status' => 'batal',
    //             'keterangan' => $request->keterangan,
    //         ]);
    //     });

    //     return redirect()
    //         ->route('atk.history')
    //         ->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan');
    // }
}
