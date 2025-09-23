<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ListSparePartModel;
use App\Models\LocationsModel;
use App\Models\SubCategoryModel;
use App\Models\SuratPesananDetailModel;
use App\Models\SuratPesananHeaderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratpesananController extends Controller
{
     public function index(Request $request)
    {
        $data['getRecord'] = SuratPesananHeaderModel::getRecord($request);

        return view('dashboard.suratpesanan.index', $data);
    }

    public function create()
    {
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = SuratPesananHeaderModel::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_surat_pesanan, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "SP/{$tahun}/{$nextNumber}";

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();

        return view('dashboard.suratpesanan.create', compact('noDokumen', 'locations', 'categories'));
    }

    // public function store(Request $request)
    // {
    //     // Validasi input dasar
    //     $request->validate([
    //         'name'        => 'required|string|max:100',
    //     ], [
    //         'name.required' => 'Form ini harus diisi',
    //     ]);


    //     // dd($request->all());

    //     // Simpan semua dalam transaksi
    //     return DB::transaction(function () use ($request) {

    //         // Simpan header surat pesanan
    //         $header = SuratPesananHeaderModel::create([
    //             'no_surat_pesanan' => $request->no_surat_pesanan,
    //             'name'             => $request->name,
    //             'locations_id'     => $request->locations_id,
    //             'category_id'      => $request->category_id,
    //             'subcategory_id'   => $request->subcategory_id,
    //         ]);

    //         // Loop semua spare part untuk simpan detail
    //         foreach ($request->product as $i => $spare_part_id) {
    //             // Ambil nama sparepart dari master (optional)
    //             $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

    //             SuratPesananDetailModel::create([
    //                 'surat_pesanan_header_id' => $header->id,   // wajib ada
    //                 'spare_part_id'           => $spare_part_id,
    //                 'qty'                     => $request->demand[$i] ?? 0,
    //                 'stock'                   => $request->stock[$i] ?? 0,  // ambil dari form
    //             ]);
    //         }

    //         return redirect()->route('suratpesanan.index')
    //             ->with('success', 'Surat pesanan berhasil dicatat.');
    //     });
    // }

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
            $header = SuratPesananHeaderModel::create([
                'no_surat_pesanan' => $request->no_surat_pesanan,
                'name'             => $request->name,
                'locations_id'     => $request->locations_id,
                'category_id'      => $request->category_id,
                'subcategory_id'   => $request->subcategory_id,
            ]);

            // Loop semua spare part untuk simpan detail
            foreach ($request->product as $i => $spare_part_id) {
                // Ambil nama sparepart dari master (optional)
                $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

                SuratPesananDetailModel::create([
                    'surat_pesanan_header_id' => $header->id,   // wajib ada
                    'spare_part_id'           => $spare_part_id,
                    'qty'                     => $request->demand[$i] ?? 0,
                    'stock'                   => $request->stock[$i] ?? 0,  // ambil dari form
                ]);
            }

            return redirect()->route('suratpesanan.index')
                ->with('success', 'Surat pesanan berhasil dicatat.');
        });
    }

    public function edit($id)
    {
        $transaction = SuratPesananHeaderModel::with([
            'details',
            'category',
            'location',
            'subcategory'
        ])->findOrFail($id);

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();
        $subcategories = SubCategoryModel::all();

        $spareparts = ListSparePartModel::orderBy('name')->get();

        return view('dashboard.suratpesanan.edit', compact('transaction', 'spareparts', 'locations', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'locations_id'   => 'required|integer|exists:locations,id',
            'category_id'    => 'required|integer|exists:category,id',
            'subcategory_id' => 'required|integer|exists:subcategory,id',
        ]);

        return DB::transaction(function () use ($request, $id) {
            // 🔹 Update header
            $header = SuratPesananHeaderModel::findOrFail($id);
            $header->update([
                'name'           => $request->name,
                'locations_id'   => $request->locations_id,
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
            ]);

            // 🔹 Ambil semua ID detail yang ada di request
            $detailIds = collect($request->details)->pluck('id')->filter()->toArray();

            // 🔹 Hapus detail yang tidak ada di request
            SuratPesananDetailModel::where('surat_pesanan_header_id', $id)
                ->whereNotIn('id', $detailIds)
                ->delete();

            // 🔹 Update atau insert detail lama
            foreach ($request->details as $detailData) {
                if (!empty($detailData['id'])) {
                    // Update existing
                    $detail = SuratPesananDetailModel::findOrFail($detailData['id']);
                    $detail->update([
                        'spare_part_id' => $detailData['spare_part_id'],
                        'qty'           => $detailData['qty'],
                        'stock'         => $detailData['stock'] ?? $detail->stock,
                    ]);
                } else {
                    // Insert baru (kalau ada baris tambahan manual)
                    SuratPesananDetailModel::create([
                        'surat_pesanan_header_id' => $id,
                        'spare_part_id'           => $detailData['spare_part_id'],
                        'qty'                     => $detailData['qty'],
                        'stock'                   => $detailData['stock'] ?? 0,
                    ]);
                }
            }

            // 🔹 Insert dari product[] tambahan
            if ($request->has('product')) {
                foreach ($request->product as $i => $spare_part_id) {
                    SuratPesananDetailModel::create([
                        'surat_pesanan_header_id' => $id,
                        'spare_part_id'           => $spare_part_id,
                        'qty'                     => $request->demand[$i] ?? 0,
                        'stock'                   => $request->stock[$i] ?? 0,
                    ]);
                }
            }

            return redirect()->route('suratpesanan.index')
                ->with('success', 'Surat pesanan berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        $suratpesanan = SuratPesananHeaderModel::findorFail($id);

        $suratpesanan->delete();

        return redirect()->route('suratpesanan.index')->with('success', 'Surat pesanan berhasil dicatat.');
    }

    public function show($id)
    {

        $transaction = SuratPesananHeaderModel::with('details')->find($id);
        // dd($transaction->details);

        // $transaction = SuratPesananHeaderModel::with(['details.sparePart'])->findOrFail($id);

        // // dd($transaction->details->toArray());

        return view('dashboard.suratpesanan.show', compact('transaction'));
    }

    public function printPdf($id)
    {
        $transaction = SuratPesananHeaderModel::with('details.sparePart')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.suratpesanan.pdf', compact('transaction'));

        return $pdf->stream(); // buka di browser
    }

    public function getStock($id)
    {
        $sparePart = ListSparePartModel::find($id);
        return response()->json([
            'stock' => $sparePart ? $sparePart->stock : 0
        ]);
    }

     public function submit($id)
    {
        $header = SuratPesananHeaderModel::findOrFail($id);
        $header->status = 'onprogress';
        $header->save();

        return back()->with('success', 'Surat pesanan diajukan untuk approval.');
    }

    public function approve($id)
    {
        $header = SuratPesananHeaderModel::findOrFail($id);
        $header->status = 'approved';
        $header->save();

        return back()->with('success', 'Surat pesanan disetujui.');
    }

    public function reject($id)
    {
        $header = SuratPesananHeaderModel::findOrFail($id);
        $header->status = 'rejected';
        $header->save();

        return back()->with('success', 'Surat pesanan ditolak.');
    }
}
