<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ListSparePartModel;
use App\Models\LocationsModel;
use App\Models\StockInHeader;
use App\Models\StockTransactionModel;
use App\Models\SubCategoryModel;
use App\Models\SuratPesananDetailModel;
use App\Models\SuratPesananHeaderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class SuratPesananV2Controller extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SuratPesananHeaderModel::getRecord($request);

        return view('dashboard.suratpesananv2.index', $data);
    }

    public function create()
    {
        $tahun = now()->format('Y');
        $bulanAngka = now()->format('m');
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

        // --- BAGIAN YANG DIPERBAIKI (Mencari 082 agar jadi 083) ---
        $lastRecord = SuratPesananHeaderModel::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastRecord) {
            // Kita pecah SP/II/2026/082/JF-01 berdasarkan "/"
            $parts = explode('/', $lastRecord->no_surat_pesanan);

            // Ambil indeks ke-3 (yaitu 082)
            if (isset($parts[3])) {
                $lastNumber = (int) $parts[3];
            }
        }

        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "SP/{$bulan}/{$tahun}/{$nextNumber}";
        // Sekarang $noDokumen akan berisi SP/II/2026/083
        // -------------------------------------------------------

        // Ambil nomor terakhir per inisial (JF, WD, dll) untuk ekornya
        // Ambil semua data tahun ini
        $lastNumbers = SuratPesananHeaderModel::whereYear('created_at', $tahun)
            ->get() // Ambil semua data tahun ini dulu
            ->groupBy('ditujukan_kepada')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    $noSurat = $item->no_surat_pesanan;
                    // Cari posisi tanda minus terakhir
                    $lastDash = strrpos($noSurat, '-');

                    if ($lastDash !== false) {
                        // Ambil angka setelah tanda minus (misal JF-01 -> ambil 01)
                        return (int) substr($noSurat, $lastDash + 1);
                    }
                    return 0;
                })->max(); // Ambil angka paling tinggi
            });

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();

        return view('dashboard.suratpesananv2.create', compact('noDokumen', 'lastNumbers', 'locations', 'categories'));
    }

    // public function create()
    // {
    //     $tahun = now()->format('Y');
    //     $bulanAngka  = now()->format('m');

    //     // Konversi bulan ke angka Romawi
    //     $romawi = [
    //         '01' => 'I',
    //         '02' => 'II',
    //         '03' => 'III',
    //         '04' => 'IV',
    //         '05' => 'V',
    //         '06' => 'VI',
    //         '07' => 'VII',
    //         '08' => 'VIII',
    //         '09' => 'IX',
    //         '10' => 'X',
    //         '11' => 'XI',
    //         '12' => 'XII',
    //     ];

    //     $bulan = $romawi[$bulanAngka];

    //     $last = SuratPesananHeaderModel::whereYear('created_at', $tahun)
    //         ->orderBy('id', 'desc')
    //         ->first();

    //     // Ambil nomor urut terakhir
    //     $lastNumber = 0;
    //     if ($last && preg_match('/(\d{3})$/', $last->no_surat_pesanan, $matches)) {
    //         $lastNumber = (int) $matches[1];
    //     }

    //     // Generate nomor baru
    //     $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    //     $noDokumen = "SP/{$bulan}/{$tahun}/{$nextNumber}";

    //     $locations = LocationsModel::all();
    //     $categories = CategoryModel::all();
    //     $subcategories = SubCategoryModel::all();

    //     return view('dashboard.suratpesananv2.create', compact('noDokumen', 'locations', 'categories'));
    // }

    public function store(Request $request)
    {
        // dd($request->all());
        // 1. Validasi tetap sama
        $request->validate([
            'name'          => 'required|string|max:100',
            'locations_id'  => 'required|integer|exists:locations,id',
            'category_id'   => 'required|integer|exists:category,id',
            'ditujukan_kepada' => 'required|string',
        ], [
            'name.required'          => 'Form ini harus diisi',
            'locations_id.required'  => 'Lokasi wajib dipilih',
            'category_id.required'   => 'Kategori wajib dipilih',
            'ditujukan_kepada.required' => 'Ditujukan Kepada wajib dipilih',
        ]);

        $barangMasihAdaStok = [];

        foreach ($request->product as $i => $spare_part_id) {
            $qtyKurang = $request->qty_kurang[$i] ?? 0;

            // Jika qty_kurang <= 0, berarti stok gudang >= permintaan
            if ($qtyKurang <= 0) {
                $namaBarang = $request->product_name[$i] ?? 'Barang pada baris ' . ($i + 1);
                $barangMasihAdaStok[] = $namaBarang;
            }
        }

        // Jika ditemukan barang yang stoknya masih ada, hentikan proses dan kirim alert
        if (!empty($barangMasihAdaStok)) {
            $listBarang = implode(', ', $barangMasihAdaStok);
            return redirect()->back()
                ->withInput() // Agar data form tidak hilang
                ->with('error', "Pemesanan Ditolak: Barang ($listBarang) masih tersedia di gudang. Silakan hapus dari list atau sesuaikan Qty Minta.");
        }

        return DB::transaction(function () use ($request) {

            // 2. Simpan header SURAT PESANAN (Tanpa bikin Surat Masuk)
            $headerPesanan = SuratPesananHeaderModel::create([
                'no_surat_pesanan' => $request->no_surat_pesanan,
                'name'             => $request->name,
                'locations_id'     => $request->locations_id,
                'category_id'      => $request->category_id,
                'subcategory_id'   => $request->subcategory_id,
                'tanggal'          => $request->tanggal,
                'status'           => 'pending', // Status awal pesanan
                'status_penerimaan' => 'open',
                'ditujukan_kepada' => $request->ditujukan_kepada,
            ]);

            // 3. Loop spare part HANYA untuk detail pesanan
            foreach ($request->product as $i => $spare_part_id) {
                SuratPesananDetailModel::create([
                    'surat_pesanan_header_id' => $headerPesanan->id,
                    'spare_part_id'           => $spare_part_id,
                    'qty'                     => $request->demand[$i] ?? 0,
                    'stock'                   => $request->stock[$i] ?? 0,
                    'qty_kurang'              => $request->qty_kurang[$i] ?? 0,
                    'keterangan'              => $request->keterangan[$i] ?? null,
                ]);
            }

            return redirect()->route('v2suratpesanan.index')
                ->with('success', 'Surat pesanan berhasil disimpan dan menunggu persetujuan.');
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

        return view('dashboard.suratpesananv2.edit', compact('transaction', 'spareparts', 'locations', 'categories', 'subcategories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'locations_id'   => 'required|integer|exists:locations,id',
            'category_id'    => 'required|integer|exists:category,id',
            // 'subcategory_id' => 'required|integer|exists:subcategory,id',
        ]);

        return DB::transaction(function () use ($request, $id) {
            // 🔹 Update header
            $header = SuratPesananHeaderModel::findOrFail($id);
            $header->update([
                'name'           => $request->name,
                'locations_id'   => $request->locations_id,
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id ?? null,
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
                        'qty_kurang'    => $detailData['qty_kurang'],
                        'keterangan'    => $detailData['keterangan'],
                    ]);
                } else {
                    // Insert baru (kalau ada baris tambahan manual)
                    SuratPesananDetailModel::create([
                        'surat_pesanan_header_id' => $id,
                        'spare_part_id'           => $detailData['spare_part_id'],
                        'qty'                     => $detailData['qty'],
                        'stock'                   => $detailData['stock'] ?? 0,
                        'qty_kurang'              => $detailData['qty_kurang'],
                        'keterangan'              => $detailData['keterangan'],
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
                        'qty_kurang'              => $request->qty_kurang[$i] ?? 0,
                        'keterangan'              => $request->keterangan[$i] ?? null,
                    ]);
                }
            }

            return redirect()->route('v2suratpesanan.index')
                ->with('success', 'Surat pesanan berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        $suratpesanan = SuratPesananHeaderModel::findorFail($id);

        $suratpesanan->delete();

        return redirect()->route('v2suratpesanan.index')->with('success', 'Surat pesanan berhasil dihapus.');
    }

    public function show($id)
    {

        $transaction = SuratPesananHeaderModel::with('details')->find($id);
        // dd($transaction->details);

        // $transaction = SuratPesananHeaderModel::with(['details.sparePart'])->findOrFail($id);

        // // dd($transaction->details->toArray());

        return view('dashboard.suratpesananv2.show', compact('transaction'));
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
        return DB::transaction(function () use ($id) {
            // 1. Ambil data pesanan beserta detail barangnya
            // Pastikan di Model SuratPesananHeaderModel sudah ada relasi public function details()
            $header = SuratPesananHeaderModel::with('details')->findOrFail($id);

            // Proteksi: Jika sudah approved, jangan diproses lagi supaya tidak double create
            if ($header->status === 'approved') {
                return back()->with('error', 'Surat pesanan ini sudah pernah disetujui.');
            }

            // Cek apakah ada detailnya? Kalau kosong, jangan lanjut.
            if ($header->details->isEmpty()) {
                return back()->with('error', 'Gagal: Surat pesanan tidak memiliki item/barang.');
            }

            // 2. Update status surat pesanan
            $header->status = 'approved';
            $header->status_penerimaan = 'proses'; // <--- TAMBAHKAN INI
            $header->save();

            // 3. Logika Generate Nomor Surat Masuk (Contoh: WH/IN/2026/001)
            $tahun = now()->format('Y');
            $count = StockInHeader::whereYear('tanggal', $tahun)->count();
            do {
                $count++;
                $nextNumber = str_pad($count, 3, '0', STR_PAD_LEFT);
                $noDokumenMasuk = "WH/IN/{$tahun}/{$nextNumber}";
            } while (StockInHeader::where('no_dokumen', $noDokumenMasuk)->exists());

            // 4. Buat Header Surat Masuk (Status: Draft)
            $headerMasuk = StockInHeader::create([
                'no_dokumen'       => $noDokumenMasuk,
                'surat_pesanan_id' => $header->id,
                'locations_id'     => $header->locations_id,
                'tanggal'          => now(),
                'status'           => 'Proses',
                // 'diterima_dari'    => $header->name,
                'diterima_oleh'    => Auth::user()->name ?? 'System',
                'referensi'       => $header->no_surat_pesanan,
            ]);

            // 5. Loop: Ambil data dari Detail Pesanan, masukkan ke Detail Surat Masuk
            foreach ($header->details as $detail) {
                StockTransactionModel::create([
                    'stock_in_header_id' => $headerMasuk->id,
                    'spare_part_id'      => $detail->spare_part_id,
                    'quantity'           => $detail->qty_kurang, // Ambil qty dari pesanan
                    'user'               => Auth::user()->name ?? 'System',
                    'keterangan'         => $detail->keterangan,
                    'status'             => 'proses',
                ]);
            }

            return back()->with('success', 'Surat pesanan disetujui dan draf surat masuk berhasil dibuat.');
        });
    }

    public function reject(Request $request, $id)
    {
        $header = SuratPesananHeaderModel::findOrFail($id);
        $header->status = 'rejected';
        $header->status_penerimaan = 'closed';
        $header->keterangan = $request->alasan_reject;
        $header->save();

        return back()->with('success', 'Surat pesanan ditolak.');
    }

    public function items($id)
    {
        try {
            // 1. Eager load relasi sparePart (P besar)
            $sp = SuratPesananHeaderModel::with(['items.sparePart'])->findOrFail($id);

            $data = $sp->items->map(function ($item) {
                // 2. Ambil nama dari kolom 'name' di ListSparePartModel melalui relasi sparePart
                $namaItem = $item->sparePart->name ?? "Spare Part ID: " . $item->spare_part_id;

                return [
                    'sparepart_id' => $item->spare_part_id,
                    'nama'         => $namaItem,
                    'qty_sisa'     => $item->qty_kurang ?? $item->qty, // Menggunakan qty_kurang dari tabel detail
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
