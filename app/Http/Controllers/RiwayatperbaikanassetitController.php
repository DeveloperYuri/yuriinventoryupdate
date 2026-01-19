<?php

namespace App\Http\Controllers;

use App\Models\AssetitModel;
use App\Models\LocationsModel;
use App\Models\RiwayatperbaikanassetitModel;
use Illuminate\Http\Request;

class RiwayatperbaikanassetitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = RiwayatperbaikanassetitModel::getRecord($request);

        return view('dashboard.assetit.riwayatperbaikanassetit.index', compact('getRecord'));
    }

    public function create()
    {
        $locations = LocationsModel::all();
        $assets = AssetitModel::orderBy('nomer_asset')->get();

        return view('dashboard.assetit.riwayatperbaikanassetit.create', compact('locations', 'assets'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'user' => 'required|string',
            'locations_id' => 'required|integer',
            'kerusakan' => 'required|string',
            'perbaikan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|string',
        ], [
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 5MB',
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'nama' => 'Nama Asset IT wajib diisi',
            'user' => 'User Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            'kerusakan' => 'Kerusakan Asset IT wajib diisi',
            'perbaikan' => 'Perbaikan Asset IT wajib diisi',
            'tanggal_mulai' => 'Tanggal mulai perbaikan wajib diisi',
            'status' => 'Status Perbaikan Asset IT wajib diisi',
        ]);

        $data = $request->only('nomer_asset', 'image', 'nama', 'user', 'locations_id', 'kerusakan', 'perbaikan', 'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        RiwayatperbaikanassetitModel::create($data);

        return redirect()->route('perbaikanasset-it.index')->with('success', 'Data perbaikan asset IT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatperbaikanassetit.edit', compact('riwayatperbaikanassetit', 'locations'));
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'user' => 'required|string',
            'locations_id' => 'required|integer',
            'kerusakan' => 'required|string',
            'perbaikan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|string',
        ], [
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'nama' => 'Nama Asset IT wajib diisi',
            'user' => 'User Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            'kerusakan' => 'Kerusakan Asset IT wajib diisi',
            'perbaikan' => 'Perbaikan Asset IT wajib diisi',
            'tanggal_mulai' => 'Tanggal mulai perbaikan wajib diisi',
            'status' => 'Status Perbaikan Asset IT wajib diisi',

        ]);

        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);
        $riwayatperbaikanassetit->nomer_asset = $request->nomer_asset;
        $riwayatperbaikanassetit->nama = $request->nama;
        $riwayatperbaikanassetit->user = $request->user;
        $riwayatperbaikanassetit->locations_id = $request->locations_id;
        $riwayatperbaikanassetit->kerusakan = $request->kerusakan;
        $riwayatperbaikanassetit->perbaikan = $request->perbaikan;
        $riwayatperbaikanassetit->tanggal_mulai = $request->tanggal_mulai;
        $riwayatperbaikanassetit->tanggal_selesai = $request->tanggal_selesai;
        $riwayatperbaikanassetit->status = $request->status;
        $riwayatperbaikanassetit->keterangan = $request->keterangan;

        if ($request->hasFile('image')) {
            if ($riwayatperbaikanassetit->image && file_exists(public_path('images/' . $riwayatperbaikanassetit->image))) {
                unlink(public_path('images/' . $riwayatperbaikanassetit->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $riwayatperbaikanassetit->image = $imageName;
        }

        $riwayatperbaikanassetit->save();

        return redirect()->route('perbaikanasset-it.index')->with('success', 'Data Perbaikan Asset IT berhasil diperbarui.');
    }

    public function show($id){
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatperbaikanassetit.show', compact('riwayatperbaikanassetit', 'locations'));
    }

    public function destroy($id)
    {
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($riwayatperbaikanassetit->image && file_exists(public_path('images/' . $riwayatperbaikanassetit->image))) {
            unlink(public_path('images/' . $riwayatperbaikanassetit->image));
        }

        $riwayatperbaikanassetit->delete();

        return redirect()->route('perbaikanasset-it.index')->with('success', 'Riwayat Perbaikan Asset IT berhasil dihapus.');
    }

    public function ajaxDetail(Request $request)
    {
        $asset = AssetitModel::with(['user', 'location'])
            ->findOrFail($request->asset_id);

        return response()->json([
            'nama_asset' => $asset->nama_asset,
            'user' => $asset->user->name ?? '-',
            'lokasi' => $asset->location->name ?? '-',
            'status' => $asset->status,
        ]);
    }
}
