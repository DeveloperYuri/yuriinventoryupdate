<?php

namespace App\Http\Controllers;

use App\Exports\AssetToolsExport;
use App\Models\ListAssetToolsModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ListAssetToolsController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = ListAssetToolsModel::getRecord($request);
        return view('dashboard.assettools.listassettools', $data);
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = ListAssetToolsModel::getRecordCard($request);
        return view('dashboard.assettools.cardlistassettools', $data);
    }


    public function create()
    {
        return view('dashboard.assettools.createassettools');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ],[
            'name' => 'Nama Asset Tools wajib diisi',
            'price.required' => 'Harga Asset Tools wajib diisi',
            'price.integer' => 'Harga Asset Tools harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 5MB',
        ]);

        $data = $request->only('name', 'price', 'satuan');

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
        return view('dashboard.assettools.editassettools', compact('assetTools'));
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
        $assetTools->satuan = $request->satuan;

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
