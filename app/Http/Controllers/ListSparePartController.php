<?php

namespace App\Http\Controllers;

use App\Exports\SparepartExport;
use App\Imports\SparePartImport;
use App\Models\ListSparePartModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ListSparePartController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = ListSparePartModel::getRecord($request);
        return view('dashboard.sparepart.listsparepart', $data);
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = ListSparePartModel::getRecordCard($request);
        return view('dashboard.sparepart.cardlistsparepart', $data);
    }

    public function create()
    {
        return view('dashboard.sparepart.createlistsparepart');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            // 'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'price.required' => 'Harga Spare Part wajib diisi',
            'price.integer' => 'Harga Spare Part harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $data = $request->only('name', 'price', 'satuan', 'numbers');

        // ✅ Generate part_number berdasarkan kata pertama dari name
        $jenis = strtolower(strtok($request->name, ' ')); // ambil kata pertama
        $count = ListSparePartModel::where('numbers', 'like', $jenis . '-%')->count() + 1;
        $increment = str_pad($count, 3, '0', STR_PAD_LEFT);
        $data['numbers'] = $jenis . '-' . $increment;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        ListSparePartModel::create($data);

        return redirect()->route('spare-parts.index')->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sparePart = ListSparePartModel::findOrFail($id);
        return view('dashboard.sparepart.editsparepart', compact('sparePart'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            // 'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'price.required' => 'Harga Spare Part wajib diisi',
            // 'price.integer' => 'Harga Spare Part harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $sparePart = ListSparePartModel::findOrFail($id);
        $sparePart->name = $request->name;
        $sparePart->price = $request->price;
        $sparePart->satuan = $request->satuan;
        $sparePart->numbers = $request->numbers;


        if ($request->hasFile('image')) {
            if ($sparePart->image && file_exists(public_path('images/' . $sparePart->image))) {
                unlink(public_path('images/' . $sparePart->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $sparePart->image = $imageName;
        }

        $sparePart->save();

        return redirect()->route('spare-parts.index')->with('success', 'Spare part berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $sparePart = ListSparePartModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($sparePart->image && file_exists(public_path('images/' . $sparePart->image))) {
            unlink(public_path('images/' . $sparePart->image));
        }

        $sparePart->delete();

        return redirect()->route('spare-parts.index')->with('success', 'Spare part berhasil dihapus.');
    }

    public function cetakPDF()
    {
        $spareparts = ListSparePartModel::all();
        $pdf = Pdf::loadView('sparepartpdf.sparepart', compact('spareparts'));
        return $pdf->download('laporan_sparepart.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new SparepartExport, 'laporan_sparepart.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Ambil semua data excel dulu
        $data = Excel::toCollection(new SparePartImport, $request->file('file'))->first();

        // Ambil semua nama di Excel
        $names = collect($data)->pluck('name')->filter()->unique();

        // Cek duplikat di database
        $existing = \App\Models\ListSparePartModel::whereIn('name', $names)->pluck('name');

        if ($existing->isNotEmpty()) {
            return redirect()->back()->withErrors([
                'file' => 'Nama berikut sudah ada: ' . $existing->implode(', ')
            ]);
        }

        // Lanjutkan import jika tidak ada duplikat
        Excel::import(new SparePartImport, $request->file('file'));

        return redirect()->route('spare-parts.index')
            ->with('success', 'Import data spare part berhasil!');
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('term'); // jQuery UI pakai "term" sebagai key
        $data = ListSparePartModel::where('name', 'LIKE', "%{$query}%")
            ->pluck('name'); // ambil hanya kolom name

        return response()->json($data);
    }
}
