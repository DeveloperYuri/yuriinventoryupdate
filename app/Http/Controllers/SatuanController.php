<?php

namespace App\Http\Controllers;

use App\Models\SatuanModel;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SatuanModel::getRecord($request);

        return view('dashboard.configuration.satuan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.configuration.satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required',
        ]);

        $data = new SatuanModel();

        $data->name = trim($request->name);
        $data->save();

        return redirect()->route('index.satuan')->with('success', 'Create data satuan berhasil!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = SatuanModel::findOrFail($id);

        return view('dashboard.configuration.satuan.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = SatuanModel::findorFail($id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect()->route('index.satuan')->with('success', 'Edit data satuan berhasil!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = SatuanModel::findorFail($id);
        $data->delete();

        return redirect()->route('index.satuan')->with('success', 'Delete data satuan berhasil!');
    }
}
