<?php

namespace App\Http\Controllers;

use App\Models\SatuanModel;
use App\Models\SparepartitModel;
use App\Models\SparepartittrasactionModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SparepartitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = SparepartitModel::getRecord($request);

        return view('dashboard.sparepartit.index', compact('getRecord'));
    }

    public function create()
    {
        $satuan = SatuanModel::all();

        return view('dashboard.sparepartit.create', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:spare_parts,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'name.unique'     => 'Nama Spare Part sudah ada',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $data = $request->only('name', 'satuan_id',);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        SparepartitModel::create($data);

        return redirect()->route('sparepart-it.index')->with('success', 'Spare part IT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sparePart = SparepartitModel::findOrFail($id);
        $satuans = SatuanModel::all();

        return view('dashboard.sparepartit.edit', compact('sparePart', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $sparePart = SparepartitModel::findOrFail($id);
        $sparePart->name = $request->name;
        $sparePart->satuan_id = $request->satuan_id;

        if ($request->hasFile('image')) {
            if ($sparePart->image && file_exists(public_path('images/' . $sparePart->image))) {
                unlink(public_path('images/' . $sparePart->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $sparePart->image = $imageName;
        }

        $sparePart->save();

        return redirect()->route('sparepart-it.index')->with('success', 'Spare part IT berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $sparePart = SparepartitModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($sparePart->image && file_exists(public_path('images/' . $sparePart->image))) {
            unlink(public_path('images/' . $sparePart->image));
        }

        $sparePart->delete();

        return redirect()->route('sparepart-it.index')->with('success', 'Spare part IT berhasil dihapus.');
    }

    public function autocomplete(Request $request)
    {
        $q = $request->get('term');

        return SparepartitModel::where('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(function ($sp) {
                return [
                    'label' => $sp->name, // tampilan dropdown
                    'value' => $sp->name, // isi input
                    'id'    => $sp->id,   // opsional
                ];
            });
    }

    public function cetakPDF()
    {
        $spareparts = SparepartitModel::all();
        $pdf = Pdf::loadView('dashboard.sparepartit.sparepartitpdf', compact('spareparts'));
        return $pdf->download('laporan_sparepart.pdf');
    }

    // public function exportExcel()
    // {
    //     return Excel::download(new SparepartExport, 'laporan_sparepart.xlsx');
    // }

    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([], 200);
        }

        $results = SparepartitModel::whereNotNull('name')
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

    public function viewHistoryPerItem(Request $request, $id)
    {
        $sparePart = SparepartitModel::findOrFail($id);

        // 1️⃣ HITUNG STOK HANYA STATUS SUKSES
        $calcQuery = SparepartittrasactionModel::where('sparepartit_id', $id)
            ->where('status', 'sukses');

        if ($request->start_date) {
            $calcQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $calcQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $calcTransactions = $calcQuery->orderBy('created_at')->get();

        $runningStock = 0;
        $runningValue = 0;

        foreach ($calcTransactions as $item) {
            if ($item->type === 'in') {
                $runningStock += $item->quantity;
                $runningValue += $item->quantity * ($item->price ?? 0);
            } else {
                $runningStock -= $item->quantity;
                $runningValue -= $item->quantity * ($item->price ?? 0);
            }
        }

        // 2️⃣ HISTORY SEMUA STATUS
        $transactions = SparepartittrasactionModel::with('sparePart')
            ->where('sparepartit_id', $id)
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.sparepartit.detail', [
            'sparePart'        => $sparePart,
            'transactions'     => $transactions,
            'lastRunningStock' => $runningStock,
            'lastRunningValue' => $runningValue,
        ]);
    }
}
