<?php

namespace App\Http\Controllers;

use App\Models\AssetitModel;
use App\Models\LocationsModel;
use App\Models\RiwayatpeminjamanassetitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatpeminjamanassetitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = RiwayatpeminjamanassetitModel::getRecord($request);

        return view('dashboard.assetit.riwayatpeminjamanassetit.index', compact('getRecord'));
    }

    public function create()
    {
        $assets    = AssetitModel::select('nomer_asset')->get();
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatpeminjamanassetit.create', compact('assets', 'locations'));
    }


    // public function create()
    // {
    //     $locations = LocationsModel::all();
    //     $riwayatpeminjamanassetsit = RiwayatpeminjamanassetitModel::orderBy('nomer_asset')->get();

    //     return view('dashboard.assetit.riwayatpeminjamanassetit.create', compact('locations', 'riwayatpeminjamanassetsit'));
    // }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            // 'nomer_asset' => 'required|string',
            // 'nama' => 'required|string',
            // 'user' => 'required|string',
            // 'locations_id' => 'required|integer',
            'tanggal_pinjam' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            // 'nomer_asset' => 'Nomor Asset IT wajib diisi',
            // 'nama' => 'Nama Asset IT wajib diisi',
            // 'user' => 'User Asset IT wajib diisi',
            // 'locations_id' => 'Lokasi Asset IT wajib diisi',
            'tanggal_pinjam' => 'Tanggal mulai perbaikan wajib diisi',
            'status' => 'Status Perbaikan Asset IT wajib diisi',
            'keterangan' => 'Keterangan Perabaikan Asset IT wajib diisi',

        ]);

        $data = $request->only('asset_id', 'nomer_asset', 'nama', 'user', 'locations_id', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'keterangan');

        RiwayatpeminjamanassetitModel::create($data);

        AssetitModel::where('nomer_asset', $request->nomer_asset)
            ->update([
                'status' => $request->status === 'Di Pinjam'
                    ? 'Dipinjam'
                    : 'Tersedia'
            ]);

        return redirect()->route('peminjamanasset-it.index')->with('success', 'Data peminjaman asset IT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $riwayatpeminjamanassetit = RiwayatpeminjamanassetitModel::findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatpeminjamanassetit.edit', compact('riwayatpeminjamanassetit', 'locations'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $request->validate([
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'user' => 'required|string',
            'locations_id' => 'required|integer',
            'tanggal_pinjam' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'required|string',
        ], [
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'nama' => 'Nama Asset IT wajib diisi',
            'user' => 'User Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            'tanggal_pinjam' => 'Tanggal pinjam wajib diisi',
            'status' => 'Status peminjaman Asset IT wajib diisi',
            'keterangan' => 'Keterangan peminjaman Asset IT wajib diisi',

        ]);

        DB::transaction(function () use ($request, $id) {

            // 1️⃣ Update riwayat peminjaman
            $riwayat = RiwayatpeminjamanassetitModel::findOrFail($id);

            $riwayat->update([
                'nomer_asset'     => $request->nomer_asset,
                'nama'            => $request->nama,
                'user'            => $request->user,
                'locations_id'    => $request->locations_id,
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status'          => $request->status,
                'keterangan'      => $request->keterangan,
            ]);

            // 2️⃣ Update status asset IT
            AssetitModel::where('nomer_asset', $request->nomer_asset)
                ->update([
                    'status' => $request->status === 'Di Pinjam'
                        ? 'Dipinjam'
                        : 'Tersedia'
                ]);
        });

        // $riwayatpeminjamanassetit = RiwayatpeminjamanassetitModel::findOrFail($id);
        // $riwayatpeminjamanassetit->nomer_asset = $request->nomer_asset;
        // $riwayatpeminjamanassetit->nama = $request->nama;
        // $riwayatpeminjamanassetit->user = $request->user;
        // $riwayatpeminjamanassetit->locations_id = $request->locations_id;
        // $riwayatpeminjamanassetit->tanggal_pinjam = $request->tanggal_pinjam;
        // $riwayatpeminjamanassetit->tanggal_kembali = $request->tanggal_kembali;
        // $riwayatpeminjamanassetit->status = $request->status;
        // $riwayatpeminjamanassetit->keterangan = $request->keterangan;

        // $riwayatpeminjamanassetit->save();

        return redirect()->route('peminjamanasset-it.index')->with('success', 'Data Peminjaman Asset IT berhasil diperbarui.');
    }

    public function show($id)
    {
        $riwayatpeminjamanassetit = RiwayatpeminjamanassetitModel::findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatpeminjamanassetit.show', compact('riwayatpeminjamanassetit', 'locations'));
    }

    public function destroy($id)
    {
        $riwayatpeminjamanassetit = RiwayatpeminjamanassetitModel::findOrFail($id);

        $riwayatpeminjamanassetit->delete();

        return redirect()->route('peminjamanasset-it.index')->with('success', 'Data peminjaman Asset IT berhasil dihapus.');
    }
}
