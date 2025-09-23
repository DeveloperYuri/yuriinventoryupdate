<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SubCategoryModel::getRecord($request);

        return view('dashboard.subcategory.index', $data);
    }

    public function create()
    {
        $categories = CategoryModel::all();
        return view('dashboard.subcategory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:category,id',
        ], [
            'name' => 'Nama Category Harus Diisi'
        ]);

        SubCategoryModel::create([
            'name' => $request->name,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('indexsubcategory')->with('success', 'Data Category Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $data = SubCategoryModel::findOrFail($id);

        $categories = CategoryModel::all();

        return view('dashboard.subcategory.edit', compact('data', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $data = SubCategoryModel::findOrFail($id);

        $data->update([
            'name' => $request->name,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('indexsubcategory')->with('success', 'Sub Category Berhasil Diupdate');
    }

    public function getByCategory($category_id)
    {
        $subcategories = SubCategoryModel::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }
}
