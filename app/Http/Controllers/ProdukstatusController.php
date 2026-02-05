<?php

namespace App\Http\Controllers;

use App\Models\ProdukstatusModel;
use Illuminate\Http\Request;

class ProdukstatusController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = ProdukstatusModel::getRecord($request);

        return view('dashboard.configuration.produkstatus.index', $data);
    }

    public function create()
    {
        return view('dashboard.configuration.produkstatus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ], [
            'name' => 'Field Nama Harus Diisi'
        ]);

        ProdukstatusModel::create([
            'name' => $request->name
        ]);

        return redirect()->route('produkstatus.index')->with('success', 'Data Produk Status Berhasil Dibuat');
    }

    public function edit($id)
    {
        $data = ProdukstatusModel::findOrFail($id);

        return view('dashboard.configuration.produkstatus.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = ProdukstatusModel::findOrFail($id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect()->route('produkstatus.index')->with('success', 'Produk Status Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $data = ProdukstatusModel::findOrFail($id);

        $data->delete();

        return redirect()->route('produkstatus.index')->with('success', 'Data Produk Status Berhasil Dihapus');
    }
}
