<?php

namespace App\Http\Controllers;

use App\Exports\AtkExport;
use App\Models\AtkModel;
use App\Models\AtktransactionModel;
use App\Models\SatuanModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AtkController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = AtkModel::getRecord($request);
        return view('dashboard.atk.listatk', $data);
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = AtkModel::getRecordCard($request);
        return view('dashboard.atk.cardlistatk', $data);
    }

    public function create()
    {
        $satuan = SatuanModel::all();
        return view('dashboard.atk.create', compact('satuan'));
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'name' => 'required|string',
            // 'price' => 'required|integer',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'price.required' => 'Harga Spare Part wajib diisi',
            'price.integer' => 'Harga Spare Part harus berupa angka',
            // 'image.required'   => 'File gambar harus diisi',
            // 'image.image'   => 'File harus berupa gambar',
            // 'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            // 'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $data = $request->only('name', 'price', 'satuan_id', 'stock');

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

        return view('dashboard.atk.edit', compact('atk', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            // 'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'price.required' => 'Harga Spare Part wajib diisi',
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

        $atktransactions = $query->paginate(20)->withQueryString();

        return view('dashboard.atk.history', compact('atktransactions'));
    }

    public function viewHistoryPerItem(Request $request, $id)
    {
        $sparePart = AtkModel::findOrFail($id);

        $query = AtktransactionModel::with([
            'atkKeluar.locations'
        ])->where('atk_id', $id);

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $allTransactions = $query->orderBy('created_at')->get();

        $totalStock = 0;
        $runningValue = 0;

        // Hitung total stock dan total value
        $allTransactions->each(function ($item) use (&$totalStock, &$runningValue, $sparePart) {
            if ($item->type === 'in') {
                $totalStock += $item->quantity;
                $runningValue += $item->quantity * $item->price;
            } else {
                $totalStock -= $item->quantity;
                $runningValue -= $item->quantity * $item->price;
            }
            $item->runningStock = $totalStock; // simpan ke tiap item supaya bisa tampil di view
            $item->runningValue = $runningValue; // simpan total harga
        });

        $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('dashboard.atk.detailatk', compact('sparePart', 'transactions', 'totalStock', 'allTransactions'));
    }

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
}
