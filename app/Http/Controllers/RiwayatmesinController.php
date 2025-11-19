<?php

namespace App\Http\Controllers;

use App\Models\RiwayatmesinModel;
use Illuminate\Http\Request;

class RiwayatmesinController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = RiwayatmesinModel::getRecord($request);
        return view('dashboard.riwayatmesin.index', $data);
    }

    public function create()
    {
        return view('dashboard.riwayatmesin.create');
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'tanggal' =>'required',
            'nama_mesin' =>'required',
            'running_hour' =>'required',
            'pekerjaan' =>'required',
            'pic' =>'required',
            'deskripsi' =>'required',
            'status' =>'required'
        ]);

        $data = new RiwayatmesinModel();

        $data->tanggal = trim($request->tanggal);
        $data->nama_mesin = trim($request->nama_mesin);
        $data->running_hour = trim($request->running_hour);
        $data->pekerjaan = trim($request->pekerjaan);
        $data->pic = trim($request->pic);
        $data->deskripsi = trim($request->deskripsi);
        $data->status = trim($request->status);
        $data->save();

        return redirect()->route('index.riwayatmesin')->with('success', 'Tambah Riwayat Mesin Berhasil');
    }

    public function edit(string $id)
    {
        $data = RiwayatmesinModel::findOrFail($id);
        
        return view('dashboard.riwayatmesin.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $data = RiwayatmesinModel::findorFail($id);

        $data->update([
            'tanggal' => $request->tanggal,
            'nama_mesin' => $request->nama_mesin,
            'running_hour' => $request->running_hour,
            'pekerjaan' => $request->pekerjaan,
            'pic' => $request->pic,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status
        ]);

        return redirect()->route('index.riwayatmesin')->with('success', 'Edit Riwayat Mesin Berhasil');
    }

    public function show(string $id)
    {
        $data = RiwayatmesinModel::findOrFail($id);
        
        return view('dashboard.riwayatmesin.show', compact('data'));
    }

    public function destroy(string $id)
    {
        $data = RiwayatmesinModel::findorFail($id);
        $data->delete();

        return redirect()->route('index.riwayatmesin')->with('success', 'Hapus Riwayat Mesin Berhasil');
    }


}
