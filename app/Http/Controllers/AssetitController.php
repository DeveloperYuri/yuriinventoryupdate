<?php

namespace App\Http\Controllers;

use App\Exports\AssetITExport;
use App\Models\AssetitModel;
use App\Models\LocationsModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssetitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = AssetitModel::getRecord($request);

        return view('dashboard.assetit.listassetit', compact('getRecord'));
    }

    public function generateNumber(Request $request)
    {
        $prefixMap = [
            'KOMPUTER'               => 'PCINV',
            'PRINTER'                => 'PRINV',
            'LAPTOP'                 => 'LPINV',
            'PROYEKTOR'              => 'PYINV',
            'INFRASTRUKTUR JARINGAN' => 'IFJINV',
            'PC SERVER'              => 'SVINV',
            'INFRASTRUKTUR TELPON'   => 'IFTINV',
            'INFRASTRUKTUR CCTV'     => 'IFCINV',
            'PONSEL'                 => 'PONINV',
            'LAINNYA'                => 'LININV',
        ];

        $nama = $request->nama;

        if (!isset($prefixMap[$nama])) {
            return response()->json(['number' => '']);
        }

        $prefix = $prefixMap[$nama];

        $lastAsset = AssetitModel::where('nomer_asset', 'like', $prefix . '%')
            ->orderBy('nomer_asset', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->nomer_asset, strlen($prefix));
            $newNumber = $prefix . ($lastNumber + 1);
        } else {
            $newNumber = $prefix . '30001';
        }

        return response()->json(['number' => $newNumber]);
    }

    public function create()
    {
        $locations = LocationsModel::all();

        return view('dashboard.assetit.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomer_asset' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'nama' => 'required|string',
            'locations_id' => 'required|integer',
            // 'spesifikasi' => 'required|string',
            'status' => 'required|string',
        ], [
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 5MB',
            'nama' => 'Nama Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            // 'spesifikasi' => 'Spesifikasi Asset IT wajib diisi',
            'status' => 'Status Asset IT wajib diisi',

        ]);

        $data = $request->only('nomer_asset', 'image', 'nama', 'user', 'locations_id', 'spesifikasi', 'status');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        AssetitModel::create($data);

        return redirect()->route('asset-it.index')->with('success', 'Asset IT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $assetit = AssetitModel::findOrFail($id);
        $locations = LocationsModel::all();
        $categories = AssetitModel::orderBy('nama', 'asc')
        ->pluck('nama')
        ->unique();

        return view('dashboard.assetit.edit', compact('assetit', 'locations', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'locations_id' => 'required|integer',
            // 'spesifikasi' => 'required|string',
            'status' => 'required|string',
        ], [
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'nama' => 'Nama Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            // 'spesifikasi' => 'Spesifikasi Asset IT wajib diisi',
            'status' => 'Status Asset IT wajib diisi',

        ]);

        $assetit = AssetitModel::findOrFail($id);
        $assetit->nomer_asset = $request->nomer_asset;
        $assetit->nama = $request->nama;
        $assetit->user = $request->user;
        $assetit->locations_id = $request->locations_id;
        $assetit->spesifikasi = $request->spesifikasi;
        $assetit->status = $request->status;

        if ($request->hasFile('image')) {
            if ($assetit->image && file_exists(public_path('images/' . $assetit->image))) {
                unlink(public_path('images/' . $assetit->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $assetit->image = $imageName;
        }

        $assetit->save();

        return redirect()->route('asset-it.index')->with('success', 'Asset IT berhasil diperbarui.');
    }

    public function show($id)
    {
        $assetit = AssetitModel::findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.show', compact('assetit', 'locations'));
    }


    public function destroy($id)
    {
        $assetit = AssetitModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($assetit->image && file_exists(public_path('images/' . $assetit->image))) {
            unlink(public_path('images/' . $assetit->image));
        }

        $assetit->delete();

        return redirect()->route('asset-it.index')->with('success', 'Asset IT berhasil dihapus.');
    }

    public function ajaxDetail(Request $request)
    {
        $asset = AssetitModel::where('nomer_asset', $request->nomer_asset)->first();

        if (!$asset) {
            return response()->json(null);
        }

        return response()->json([
            'nama'        => $asset->nama,
            'user'        => $asset->user,
            'location_id' => $asset->locations_id,
        ]);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->q;

        $assets = AssetitModel::where('nama', 'like', "%{$search}%")
            ->orWhere('nomer_asset', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json(
            $assets->map(function ($asset) {
                return [
                    'id'    => $asset->id,
                    'label' => $asset->nomer_asset . ' - ' . $asset->nama,
                    'value' => $asset->nomer_asset
                ];
            })
        );
    }

    public function autocompletesearch(Request $request)
    {
        $query = $request->get('term'); // jQuery UI pakai "term" sebagai key
        $data = AssetitModel::where('nomer_asset', 'LIKE', "%{$query}%")
            ->pluck('nomer_asset'); // ambil hanya kolom name

        return response()->json($data);
    }

    public function suggest(Request $request)
    {
        $q = $request->q;

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $assets = AssetitModel::where('nomer_asset', 'like', "%$q%")
            ->limit(10)
            ->get();

        return response()->json($assets->pluck('nomer_asset'));
    }

    public function cetakPDF()
    {
        $assetit = AssetitModel::with('location')->get();
        $pdf = Pdf::loadView('dashboard.assetit.pdf', compact('assetit'));
        return $pdf->download('laporan_asset_it.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new AssetITExport, 'laporan_assetit.xlsx');
    }
}
