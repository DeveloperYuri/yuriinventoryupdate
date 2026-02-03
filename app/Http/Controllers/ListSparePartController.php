<?php

namespace App\Http\Controllers;

use App\Exports\SparepartExport;
use App\Exports\SparePartMultiSheetExport;
use App\Imports\SparePartImport;
use App\Models\CategoryModel;
use App\Models\ListSparePartModel;
use App\Models\SatuanModel;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ListSparePartController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = ListSparePartModel::getRecord($request);

        $period = $request->period;

        if ($period) {
            $start = $period . '-01';
            $end   = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString(); // Sampai 23:59:59

            // AMBIL SAMPAI DETIK TERAKHIR BULAN LALU
            $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();
        }

        foreach ($getRecord as $part) {

            if ($period) {
                $stockAwal = $part->transactions()
                    ->where('created_at', '<=', $prevMonthEnd)
                    ->selectRaw("SUM(CASE WHEN type='in' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;

                $masuk  = $part->getInPeriod($start, $end);
                $keluar = $part->getOutPeriod($start, $end);
            } else {
                // Mode normal (tanpa periode)
                $stockAwal = 0;
                $masuk     = $part->getTotalIn();
                $keluar    = $part->getTotalOut();
            }

            $part->stock_awal  = $stockAwal;
            $part->masuk       = $masuk;
            $part->keluar      = $keluar;
            $part->stock_akhir = $stockAwal + $masuk - $keluar;
        }

        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();

        return view('dashboard.sparepart.listsparepart', compact('getRecord', 'categories', 'subcategories', 'period'));
    }

    public function cardindex(Request $request)
    {
        $data['getRecordCard'] = ListSparePartModel::getRecordCard($request);
        return view('dashboard.sparepart.cardlistsparepart', $data);
    }

    public function create()
    {
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();
        $satuan = SatuanModel::all();

        return view('dashboard.sparepart.createlistsparepart', compact('categories', 'subcategories', 'satuan'));
    }


    public function store(Request $request)
    {

        // dd($request -> all());
        $request->validate([
            'name' => 'required|unique:spare_parts,name',
            // 'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'name' => 'Nama Spare Part wajib diisi',
            'name.unique'     => 'Nama Spare Part sudah ada',
            'price.required' => 'Harga Spare Part wajib diisi',
            'price.integer' => 'Harga Spare Part harus berupa angka',
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 10MB',
        ]);

        $data = $request->only('name', 'price', 'satuan', 'numbers', 'category_id', 'subcategory_id', 'satuan_id');

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

        //buat di testing deploy rumah web
        // if ($request->hasFile('image')) {
        //     $imageName = time() . '.' . $request->image->extension();
        //     $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/images';
        //     $request->image->move($destinationPath, $imageName);
        //     $data['image'] = $imageName;
        // }

        ListSparePartModel::create($data);

        return redirect()->route('spare-parts.index')->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $sparePart = ListSparePartModel::findOrFail($id);

        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();
        $satuans = SatuanModel::all();


        return view('dashboard.sparepart.editsparepart', compact('sparePart', 'categories', 'subcategories', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
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

        // 1. Ambil prefix dari nama (kata pertama)
        $jenis = strtolower(strtok($request->name, ' ')); // Hasil: "seal"

        // 2. CEK: Jika numbers masih kosong ATAU nama depannya berubah
        // Kita pakai substr untuk ambil kata depan dari numbers lama (misal "seal-001" ambil "seal")
        $prefixLama = strtolower(strtok($sparePart->numbers, '-'));

        if (empty($sparePart->numbers) || $jenis !== $prefixLama) {
            $count = ListSparePartModel::where('numbers', 'like', $jenis . '-%')
                ->where('id', '!=', $id)
                ->count() + 1;

            $increment = str_pad($count, 3, '0', STR_PAD_LEFT);
            $sparePart->numbers = $jenis . '-' . $increment;
        }

        // Update data lainnya
        $sparePart->name = $request->name;
        $sparePart->price = $request->price;
        $sparePart->satuan_id = $request->satuan_id;
        $sparePart->category_id = $request->category_id;
        $sparePart->subcategory_id = $request->subcategory_id;


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

    public function cetakPDF(Request $request)
    {

        ini_set('memory_limit', '1024M'); // Naikkan ke 1GB sementara
        set_time_limit(50); // Beri waktu 5 menit

        $period = $request->period; // boleh null
        $spareparts = ListSparePartModel::all();

        if ($period) {
            $start = $period . '-01';

            // 1. Ambil titik potong detik terakhir bulan lalu (23:59:59)
            $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();

            // 2. Ambil titik potong detik terakhir bulan ini (23:59:59)
            $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString();

            foreach ($spareparts as $part) {
                // Gunakan query manual agar jam (timestamp) terhitung akurat
                $stockAwal = $part->transactions()
                    ->where('created_at', '<=', $prevMonthEnd)
                    ->selectRaw("SUM(CASE WHEN type='in' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;

                // Pastikan method ini di Model sudah support jam di variabel $end
                $masuk  = $part->getInPeriod($start, $end);
                $keluar = $part->getOutPeriod($start, $end);

                $part->stock_awal  = $stockAwal;
                $part->masuk       = $masuk;
                $part->keluar      = $keluar;
                $part->stock_akhir = $stockAwal + $masuk - $keluar;
            }

            $filename = 'laporan_sparepart_' . $period . '.pdf';
        } else {
            // MODE GLOBAL (tetap sama)
            foreach ($spareparts as $part) {
                $masuk  = $part->getTotalIn();
                $keluar = $part->getTotalOut();

                $part->stock_awal  = 0;
                $part->masuk       = $masuk;
                $part->keluar      = $keluar;
                $part->stock_akhir = $masuk - $keluar;
            }

            $filename = 'stok_sparepart_global.pdf';
        }

        $pdf = Pdf::loadView('sparepartpdf.sparepart', [
            'spareparts' => $spareparts,
            'period' => $period
        ])->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }


    public function exportExcel(Request $request)
    {

        $period = $request->period; // boleh null

        return Excel::download(
            new SparepartExport($request->period),
            'laporan_' . $request->period . '.xlsx'
        );
    }

    public function exportmultipleExcel(Request $request)
    {
        // 1. Ambil periode dari request (misal: 2026-02)
        $period = $request->period;

        // 2. Tentukan nama file
        // Jika ada periode, nama file: Laporan_Sparepart_2026-02.xlsx
        // Jika tidak ada, nama file: Laporan_Sparepart_Global.xlsx
        $filename = 'Laporan_Sparepart_' . ($period ?: 'Global') . '.xlsx';

        // 3. Eksekusi Download
        // Kita kirim variabel $period ke dalam constructor SparePartMultiSheetExport
        return Excel::download(new SparePartMultiSheetExport($period), $filename);
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
