<?php

namespace App\Http\Controllers;

use App\Exports\AssetToolsExport;
use App\Models\ListAssetToolsModel;
use App\Models\SatuanModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ListAssetToolsController extends Controller
{
    // public function index(Request $request)
    // {
    //     $data['getRecord'] = ListAssetToolsModel::getRecord($request);
    //     return view('dashboard.assettools.listassettools', $data);
    // }

    public function index(Request $request)
    {
        // 1. Ambil data dasar
        $getRecord = ListAssetToolsModel::getRecord($request);
        $period = $request->period; // format: YYYY-MM

        // 2. Siapkan variabel waktu jika ada filter periode
        if ($period) {
            $start = $period . '-01';
            // Sampai detik terakhir bulan ini
            $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString();
            // Titik potong untuk stok awal (detik terakhir bulan sebelumnya)
            $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();
        }

        // 3. Loop untuk menghitung angka-angka stok
        foreach ($getRecord as $tool) {
            if ($period) {
                // Hitung saldo kumulatif dari awal waktu sampai bulan lalu
                $stockAwal = $tool->stockTransactions() // Pastikan relasi transactions() ada di model
                    ->where('created_at', '<=', $prevMonthEnd)
                    ->selectRaw("SUM(CASE WHEN type='in' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;

                // Hitung mutasi di bulan terpilih
                $masuk  = $tool->getInPeriod($start, $end);
                $keluar = $tool->getOutPeriod($start, $end);
            } else {
                // Mode normal (Global)
                $stockAwal = 0;
                $masuk     = $tool->getTotalIn();
                $keluar    = $tool->getTotalOut();
            }

            // Tempelkan hasil hitungan ke objek
            $tool->stock_awal  = $stockAwal;
            $tool->masuk       = $masuk;
            $tool->keluar      = $keluar;
            $tool->stock_akhir = $stockAwal + $masuk - $keluar;
        }

        return view('dashboard.assettools.listassettools', compact('getRecord', 'period'));
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = ListAssetToolsModel::getRecordCard($request);
        return view('dashboard.assettools.cardlistassettools', $data);
    }

    public function create()
    {
        $satuan = SatuanModel::all();
        return view('dashboard.assettools.createassettools', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ], [
            'name' => 'Nama Asset Tools wajib diisi',
            'price.required' => 'Harga Asset Tools wajib diisi',
            'price.integer' => 'Harga Asset Tools harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 5MB',
        ]);

        $data = $request->only('name', 'price', 'satuan', 'satuan_id');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        ListAssetToolsModel::create($data);

        return redirect()->route('asset-tools.index')->with('success', 'Asset Tools berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $assetTools = ListAssetToolsModel::findOrFail($id);
        $satuans = SatuanModel::all();

        return view('dashboard.assettools.editassettools', compact('assetTools', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $assetTools = ListAssetToolsModel::findOrFail($id);
        $assetTools->name = $request->name;
        $assetTools->price = $request->price;
        $assetTools->satuan_id = $request->satuan_id;

        if ($request->hasFile('image')) {
            if ($assetTools->image && file_exists(public_path('images/' . $assetTools->image))) {
                unlink(public_path('images/' . $assetTools->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $assetTools->image = $imageName;
        }

        $assetTools->save();

        return redirect()->route('asset-tools.index')->with('success', 'Asset Tools berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $assetTools = ListAssetToolsModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($assetTools->image && file_exists(public_path('images/' . $assetTools->image))) {
            unlink(public_path('images/' . $assetTools->image));
        }

        $assetTools->delete();

        return redirect()->route('asset-tools.index')->with('success', 'Asset Tools berhasil dihapus.');
    }

    public function cetakPDF()
    {
        $assettools = ListAssetToolsModel::all();
        $pdf = Pdf::loadView('assettoolspdf.assettools', compact('assettools'));
        return $pdf->download('laporan_assettools.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new AssetToolsExport, 'laporan_assettools.xlsx');
    }
}
