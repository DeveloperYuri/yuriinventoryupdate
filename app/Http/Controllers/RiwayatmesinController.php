<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\RiwayatmesinModel;
use App\Models\SubCategoryModel;
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
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();
        return view('dashboard.riwayatmesin.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'tanggal' => 'required',
            // 'nama_mesin' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'running_hour' => 'required',
            'pekerjaan' => 'required',
            'pic' => 'required',
            'deskripsi' => 'required',
            'status' => 'required'
        ]);

        $data = new RiwayatmesinModel();

        $data->tanggal = trim($request->tanggal);
        // $data->nama_mesin = trim($request->nama_mesin);
        $data->category_id = trim($request->category_id);
        $data->subcategory_id = trim($request->subcategory_id);
        $data->running_hour = trim($request->running_hour);
        $data->pekerjaan = trim($request->pekerjaan);
        $data->pic = trim($request->pic);
        $data->deskripsi = trim($request->deskripsi);
        $data->status = trim($request->status);
        $data->tanggal_selesai = trim($request->tanggal_selesai);
        $data->save();

        return redirect()->route('index.riwayatmesin')->with('success', 'Tambah Riwayat Mesin Berhasil');
    }

    public function edit($id)
    {
        // Data utama riwayat
        $data = RiwayatmesinModel::with(['category', 'subcategory'])->findOrFail($id);

        // List Category
        $categories = CategoryModel::all();

        // List SubCategory sesuai category milik data
        $subcategories = SubCategoryModel::where('category_id', $data->category_id)->get();

        return view('dashboard.riwayatmesin.edit', compact('data', 'categories', 'subcategories'));
    }

    public function update(Request $request, string $id)
    {
        $data = RiwayatmesinModel::findorFail($id);

        $data->update([
            'tanggal' => $request->tanggal,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'running_hour' => $request->running_hour,
            'pekerjaan' => $request->pekerjaan,
            'pic' => $request->pic,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('index.riwayatmesin')->with('success', 'Edit Riwayat Mesin Berhasil');
    }

    public function show(string $id)
    {
        // Data utama riwayat
        $data = RiwayatmesinModel::with(['category', 'subcategory'])->findOrFail($id);

        // List Category
        $categories = CategoryModel::all();

        // List SubCategory sesuai category milik data
        $subcategories = SubCategoryModel::where('category_id', $data->category_id)->get();

        return view('dashboard.riwayatmesin.show', compact('data', 'categories', 'subcategories'));
    }

    public function destroy(string $id)
    {
        $data = RiwayatmesinModel::findorFail($id);
        $data->delete();

        return redirect()->route('index.riwayatmesin')->with('success', 'Hapus Riwayat Mesin Berhasil');
    }
}
