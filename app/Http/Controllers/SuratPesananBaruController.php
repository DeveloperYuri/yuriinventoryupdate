<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\LocationsModel;
use App\Models\SubCategoryModel;
use App\Models\SuratPesananBaruDetailModel;
use App\Models\SuratPesananBaruHeaderModel;
use App\Models\SuratPesananHeaderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPesananBaruController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SuratPesananBaruHeaderModel::getRecord($request);

        return view('dashboard.suratpesananbaru.index', $data);
    }

    public function create()
    {
        $tahun = now()->format('Y');
        $bulanAngka  = now()->format('m');

        // Konversi bulan ke angka Romawi
        $romawi = [
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        ];

        $bulan = $romawi[$bulanAngka];

        // Ambil record terakhir tahun ini
        $last = SuratPesananBaruHeaderModel::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulanAngka)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_surat_pesanan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "SP/{$bulan}/{$tahun}/{$nextNumber}";

        $departments = DepartmentModel::all();
        $locations = LocationsModel::all();
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();

        return view('dashboard.suratpesananbaru.create', compact('noDokumen', 'locations', 'categories', 'departments'));
    }

    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'name'          => 'required|string|max:100',
            'locations_id'  => 'required|integer|exists:locations,id',
            'department_id'  => 'required|integer|exists:department,id',
            'category_id'   => 'required|integer|exists:category,id',
            'subcategory_id' => 'required|integer|exists:subcategory,id',
        ], [
            'name.required'          => 'Form ini harus diisi',
            'department_id.required'  => 'Department wajib dipilih',
            'locations_id.required'  => 'Lokasi wajib dipilih',
            'category_id.required'   => 'Kategori wajib dipilih',
            'subcategory_id.required' => 'Subkategori wajib dipilih',
        ]);

        // dd($request->item_type);

        // dd($request->all());

        // Simpan semua dalam transaksi
        return DB::transaction(function () use ($request) {

            // Simpan header surat pesanan
            $header = SuratPesananBaruHeaderModel::create([
                'no_surat_pesanan' => $request->no_surat_pesanan,
                'name'             => $request->name,
                'department_id'     => $request->department_id,
                'locations_id'     => $request->locations_id,
                'category_id'      => $request->category_id,
                'subcategory_id'   => $request->subcategory_id,
            ]);

            foreach ($request->product_name as $i => $nama) {
                SuratPesananBaruDetailModel::create([
                    'surat_pesanan_baru_header_id' => $header->id,
                    'item_type'   => $request->item_type[$i],
                    'item_id'      => $request->item_id[$i],
                    'product_name' => $nama,
                    'qty'          => $request->demand[$i],
                    'stock'        => $request->stock[$i],
                    'qty_kurang'   => $request->qty_kurang[$i],
                    'keterangan'   => $request->keterangan[$i],
                ]);
            }

            return redirect()->route('suratpesananbaru.index')
                ->with('success', 'Surat pesanan berhasil dicatat.');
        });
    }

    public function destroy($id)
    {
        $suratpesanan = SuratPesananBaruHeaderModel::findorFail($id);

        $suratpesanan->delete();

        return redirect()->route('suratpesananbaru.index')->with('success', 'Surat pesanan berhasil dihapus.');
    }

    public function show($id)
    {
        $transaction = SuratPesananBaruHeaderModel::with([
            'details.item',   // ⬅️ INI YANG KURANG
            'category',
            'subcategory',
            'location'
        ])->findOrFail($id);

        // dd($transaction->details->first()); 

        return view('dashboard.suratpesananbaru.show', compact('transaction'));
    }


    // public function show($id)
    // {

    //     $transaction = SuratPesananBaruHeaderModel::with('details')->find($id);

    //     return view('dashboard.suratpesananbaru.show', compact('transaction'));
    // }
}
