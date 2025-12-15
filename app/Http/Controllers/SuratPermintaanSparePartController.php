<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\LocationsModel;
use App\Models\SubCategoryModel;
use App\Models\SuratPermintaanSparePartDetailModel;
use App\Models\SuratPermintaanSparePartHeaderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPermintaanSparePartController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SuratPermintaanSparePartHeaderModel::getRecord($request);

        return view('dashboard.suratpermintaansparepart.index', $data);
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
        $last = SuratPermintaanSparePartHeaderModel::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulanAngka)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_surat_permintaan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "SP/{$bulan}/{$tahun}/{$nextNumber}";

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();

        return view('dashboard.suratpermintaansparepart.create', compact('noDokumen', 'locations', 'categories'));
    }

    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'name'          => 'required|string|max:100',
            'locations_id'  => 'required|integer|exists:locations,id',
            'category_id'   => 'required|integer|exists:category,id',
            'subcategory_id' => 'required|integer|exists:subcategory,id',
        ], [
            'name.required'          => 'Form ini harus diisi',
            'locations_id.required'  => 'Lokasi wajib dipilih',
            'category_id.required'   => 'Kategori wajib dipilih',
            'subcategory_id.required' => 'Subkategori wajib dipilih',
        ]);


        // dd($request->all());

        // Simpan semua dalam transaksi
        return DB::transaction(function () use ($request) {

            // Simpan header surat pesanan
            $header = SuratPermintaanSparePartHeaderModel::create([
                'no_surat_permintaan' => $request->no_surat_permintaan,
                'name'             => $request->name,
                'locations_id'     => $request->locations_id,
                'category_id'      => $request->category_id,
                'subcategory_id'   => $request->subcategory_id,
            ]);

            // Loop semua spare part untuk simpan detail
            foreach ($request->product as $i => $spare_part_id) {
                // Ambil nama sparepart dari master (optional)
                $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

                SuratPermintaanSparePartDetailModel::create([
                    'surat_permintaan_header_id' => $header->id,   // wajib ada
                    'spare_part_id'           => $spare_part_id,
                    'qty'                     => $request->demand[$i] ?? 0,
                    'stock'                   => $request->stock[$i] ?? 0,  // ambil dari form
                    'keterangan'              => $request->keterangan[$i] ?? null,
                ]);
            }

            return redirect()->route('suratpermintaansparepart.index')
                ->with('success', 'Surat permintaan berhasil Dibuat.');
        });
    }

    public function destroy($id)
    {
        $suratpesanan = SuratPermintaanSparePartHeaderModel::findorFail($id);

        $suratpesanan->delete();

        return redirect()->route('suratpermintaansparepart.index')->with('success', 'Surat Permintaan Berhasil Dihapus.');
    }

    public function show($id)
    {

        $transaction = SuratPermintaanSparePartHeaderModel::with('details')->find($id);

        return view('dashboard.suratpermintaansparepart.show', compact('transaction'));
    }

}
