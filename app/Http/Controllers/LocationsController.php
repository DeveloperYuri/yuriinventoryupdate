<?php

namespace App\Http\Controllers;

use App\Models\LocationsModel;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = LocationsModel::getRecord($request);

        return view('dashboard.locations.index', $data);
    }

    public function create()
    {
        return view('dashboard.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ], [
            'name' => 'Nama Location harus diisi'
        ]);

        LocationsModel::create([
            'name' => $request->name
        ]);

        return redirect()->route('indexlocations')->with('success', 'Location Berhasil Ditambah');
    }

    public function edit($id)
    {
        $data = LocationsModel::findOrFail($id);

        return view('dashboard.locations.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = LocationsModel::findOrFail($id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect()->route('indexlocations')->with('success', 'Location Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $data = LocationsModel::findOrFail($id);

        $data->delete();

        return redirect()->route('indexlocations')->with('success', 'Location Berhasil Dihapus');
    }
}
