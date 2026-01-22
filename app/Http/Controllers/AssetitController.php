<?php

namespace App\Http\Controllers;

use App\Models\AssetitModel;
use App\Models\LocationsModel;
use Illuminate\Http\Request;

class AssetitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = AssetitModel::getRecord($request);

        return view('dashboard.assetit.listassetit', compact('getRecord'));
    }

    public function create()
    {
        $locations = LocationsModel::all();

        $lastAsset = AssetitModel::orderBy('nomer_asset', 'desc')->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->nomer_asset, 5); // INVTR = 5 char
            $newNumber = 'INVTR' . ($lastNumber + 1);
        } else {
            $newNumber = 'INVTR30001';
        }

        return view('dashboard.assetit.create', compact('locations', 'newNumber'));
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

        return view('dashboard.assetit.edit', compact('assetit', 'locations'));
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
}
