<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = CategoryModel::getRecord($request);

        return view('dashboard.category.index', $data);
    }

    public function create()
    {
        return view('dashboard.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ], [
            'name' => 'Nama Category Harus Diisi'
        ]);

        CategoryModel::create([
            'name' => $request->name
        ]);

        return redirect()->route('indexcategory')->with('success', 'Data Category Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $data = CategoryModel::findOrFail($id);

        return view('dashboard.category.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = CategoryModel::findOrFail($id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect()->route('indexcategory')->with('success', 'Category Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $data = CategoryModel::findOrFail($id);

        $data->delete();

        return redirect()->route('indexcategory')->with('success', 'Data Category Berhasil Dihapus');
    }
}
