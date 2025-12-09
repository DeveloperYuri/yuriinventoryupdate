<?php

namespace App\Http\Controllers;

use App\Models\DepartmentModel;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = DepartmentModel::getRecord($request);

        return view('dashboard.configuration.department.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.configuration.department.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        echo "TEST MASUK";
        $data = request()->validate([
            'name' => 'required',
        ]);

        $data = new DepartmentModel();

        $data->name = trim($request->name);
        $data->save();

        return redirect()->route('index.department')->with('success', 'Create data department berhasil!');
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
        $data = DepartmentModel::findOrFail($id);

        return view('dashboard.configuration.department.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = DepartmentModel::findorFail($id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect()->route('index.department')->with('success', 'Edit data department berhasil!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = DepartmentModel::findorFail($id);
        $data->delete();

        return redirect()->route('index.department')->with('success', 'Delete data department berhasil!');
    }
}
