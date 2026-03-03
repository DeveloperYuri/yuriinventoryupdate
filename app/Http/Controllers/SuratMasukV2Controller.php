<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ListSparePartModel;
use App\Models\LocationsModel;
use App\Models\StockInHeader;
use App\Models\StockOutHeader;
use App\Models\StockTransactionModel;
use App\Models\SupplierModel;
use App\Models\SuratPesananHeaderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratMasukV2Controller extends Controller
{
    // Spare Part In
    public function index(Request $request)
    {
        $query = StockInHeader::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.sparepartmasukv2.listsparepartinmultiple', compact('transactions'));
    }

    // public function create()
    // {
    //     // $noDokumen = 'WH43/IN/' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    //     $tahun = now()->format('Y');

    //     // Ambil record terakhir tahun ini
    //     $last = StockInHeader::whereYear('tanggal', $tahun)
    //         ->orderBy('id', 'desc')
    //         ->first();

    //     // Ambil nomor urut terakhir
    //     $lastNumber = 0;
    //     if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
    //         $lastNumber = (int) $matches[1];
    //     }

    //     // Generate nomor baru
    //     $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    //     $noDokumen = "WH/IN/{$tahun}/{$nextNumber}";

    //     $suppliers = SupplierModel::all();


    //     return view('dashboard.sparepartinmultiple.create', compact('noDokumen', 'suppliers'));
    // }

    public function create()
    {
        $tahun = now()->format('Y');
        $prefix = "WH/IN/{$tahun}/";

        // 1. Ambil SEMUA nomor dokumen tahun ini (untuk menghindari salah urutan di SQL)
        $allDocuments = StockInHeader::where('no_dokumen', 'LIKE', $prefix . '%')
            ->pluck('no_dokumen'); // Mengambil array berisi daftar nomor saja

        $highestNumber = 0;

        foreach ($allDocuments as $noDok) {
            // Bersihkan jika ada -BO (Contoh: WH/IN/2026/183-BO1 -> WH/IN/2026/183)
            $cleanNo = explode('-BO', $noDok)[0];

            // Ambil 3 digit terakhir (183)
            $currentNumber = (int) substr($cleanNo, -3);

            // Cek apakah ini yang paling besar?
            if ($currentNumber > $highestNumber) {
                $highestNumber = $currentNumber;
            }
        }

        // 2. Generate nomor baru (Misal: 183 + 1 = 184)
        $nextNumber = str_pad($highestNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = $prefix . $nextNumber;

        $suppliers = SupplierModel::all();
        return view('dashboard.sparepartmasukv2.create', compact('noDokumen', 'suppliers'));
    }

    public function storein(Request $request)
    {

        dd([
            'Tujuan_Action' => $request->action_type,
            'ID_Produk' => $request->product,
            'Sisa_Yang_Harus_Ada' => $request->demand,
            'Kenyataan_Datang' => $request->qty_datang,
            'PO_Number' => $request->po_numbers
        ]);
        // Validasi input
        $validated = $request->validate([
            'diterima_dari'  => 'required|string|max:100',
            'diterima_oleh'  => 'required|string|max:100',
        ], [
            'diterima_dari' => 'Form ini harus diisi',
            'diterima_oleh' => 'Form ini harus diisi',
        ]);

        return DB::transaction(function () use ($request) {
            $header = StockInHeader::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diterima_dari' => $request->diterima_dari,
                'diterima_oleh' => $request->diterima_oleh,
                'supplier_id' => $request->supplier_id,
                'po_numbers' => $request->po_numbers,
                'status' => 'sukses',
                // 'user'          => auth()->user()->name,
            ]);

            foreach ($request->product as $i => $spare_part_id) {
                $sparePart = ListSparePartModel::findOrFail($spare_part_id);

                StockTransactionModel::create([
                    'stock_in_header_id' => $header->id,
                    'spare_part_id'      => $spare_part_id,
                    'type'               => 'in',
                    'quantity'           => $request->demand[$i],
                    'price'              => $sparePart->price, // harga snapshot dari master
                    'user'               => $request->diterima_oleh,
                    'status' => 'sukses',
                    // 'user'               => auth()->user()->name,
                ]);
            }

            return redirect()->route('sparepartinmultiple.index')->with('success', 'Stok masuk berhasil dicatat.');
        });
    }

    public function approve(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'diterima_oleh' => 'required|string|max:255',
            'diterima_dari' => 'required|string|max:255',
        ]);

        $header = StockInHeader::findOrFail($id);
        if ($header->status !== 'Proses') return back();

        DB::beginTransaction();
        try {
            $action = $request->input('action_type');
            $backorderNeeded = false;
            $backorderItems = [];
            $pos = strpos($header->no_dokumen, '-BO');
            $baseDoc = ($pos !== false) ? substr($header->no_dokumen, 0, $pos) : $header->no_dokumen;

            foreach ($request->product as $i => $spare_part_id) {
                $qtyDatang = (int)$request->qty_datang[$i];
                $qtySeharusnya = (int)$request->demand[$i];

                $detail = StockTransactionModel::where('stock_in_header_id', $header->id)
                    ->where('spare_part_id', $spare_part_id)
                    ->first();

                if ($detail) {
                    $sisaGantung = $qtySeharusnya - $qtyDatang;

                    // 1. LOGIK BACKORDER (Tetap jalan untuk menampung sisa)
                    if ($qtyDatang < $qtySeharusnya && $action === 'backorder' && $sisaGantung > 0) {
                        $backorderNeeded = true;
                        $backorderItems[] = [
                            'spare_part_id' => $spare_part_id,
                            'quantity'      => $sisaGantung,
                            'price'         => $detail->price,
                        ];
                    }

                    // 2. LOGIK APPROVAL SEKARANG (INI PERUBAHANNYA)
                    if ($qtyDatang > 0) {
                        // Update transaksi hanya jika barangnya memang datang
                        $detail->update([
                            'quantity' => $qtyDatang,
                            'status'   => 'sukses',
                            'user'     => $request->diterima_oleh,
                            'created_at' => $request->tanggal . ' ' . now()->format('H:i:s'),
                        ]);

                        // Tambah stok ke master
                        \App\Models\ListSparePartModel::where('id', $spare_part_id)->increment('stock', $qtyDatang);
                    } else {
                        // JIKA QTY DATANG 0: Hapus detail dari header saat ini
                        // Agar tidak ada record "Sukses" dengan qty 0 di riwayat transaksi.
                        $detail->delete();
                    }
                }
            }

            // foreach ($request->product as $i => $spare_part_id) {
            //     $qtyDatang = (int)$request->qty_datang[$i];
            //     $qtySeharusnya = (int)$request->demand[$i];

            //     $detail = StockTransactionModel::where('stock_in_header_id', $header->id)
            //         ->where('spare_part_id', $spare_part_id)
            //         ->first();

            //     if ($detail) {
            //         // Selisih untuk backorder (hanya variabel lokal, tidak dikirim ke DB PO)
            //         $sisaGantung = $qtySeharusnya - $qtyDatang;

            //         // LOGIC BACKORDER (Hanya menampung ke array untuk dokumen StockIn baru)
            //         if ($qtyDatang < $qtySeharusnya && $action === 'backorder' && $sisaGantung > 0) {
            //             $backorderNeeded = true;
            //             $backorderItems[] = [
            //                 'spare_part_id' => $spare_part_id,
            //                 'quantity'      => $sisaGantung,
            //                 'price'         => $detail->price,
            //             ];
            //         }

            //         // Update detail Stock In yang sedang diproses
            //         $detail->update([
            //             'quantity' => $qtyDatang,
            //             'status'   => 'sukses',
            //             'user'     => $request->diterima_oleh,
            //             'created_at' => $request->tanggal . ' ' . now()->format('H:i:s'), // Tanggal pilih user + jam sekarang
            //         ]);

            //         // Increment Stok Sparepart
            //         if ($qtyDatang > 0) {
            //             \App\Models\ListSparePartModel::where('id', $spare_part_id)->increment('stock', $qtyDatang);
            //         }
            //     }
            // }

            // PROSES PEMBUATAN DOKUMEN BACKORDER (Jika diperlukan)
            if ($backorderNeeded && $action === 'backorder' && count($backorderItems) > 0) {

                // 1. Ambil No Dokumen Asli (bersihkan teks -BO jika ada)
                // Jika dokumen saat ini adalah WH/.../160-BO1, maka $baseDoc menjadi WH/.../160
                // $baseDoc = explode('-BO', $header->no_dokumen)[0];

                // 2. Hitung berapa banyak BO yang sudah ada untuk No Dokumen Asli ini
                $countBO = StockInHeader::where('no_dokumen', 'LIKE', $baseDoc . '-BO%')->count();
                $nextBO = $countBO + 1;

                $newHeader = StockInHeader::create([
                    'no_dokumen'    => $header->no_dokumen . '-BO' . $nextBO, // Tambah random biar unik
                    'supplier_id'   => $header->supplier_id,
                    'referensi'     => $header->referensi,
                    'tanggal'       => $request->tanggal,
                    'diterima_dari' => $header->diterima_dari,
                    'diterima_oleh' => $request->diterima_oleh,
                    'status'        => 'Proses',
                    'keterangan'    => 'Backorder dari ' . $header->no_dokumen,
                    'category_id'   => $header->category_id,
                    'location_id'   => $header->location_id,
                ]);

                foreach ($backorderItems as $item) {
                    StockTransactionModel::create([
                        'stock_in_header_id' => $newHeader->id,
                        'spare_part_id'      => $item['spare_part_id'],
                        'type'               => 'in',
                        'quantity'           => $item['quantity'],
                        'price'              => $item['price'],
                        'status'             => 'proses',
                        'user'               => $request->diterima_oleh,
                    ]);
                }
                $statusPO = 'terima sebagian';
            } else {
                $statusPO = 'closed';
            }

            // UPDATE STATUS HEADER PO (Bukan Qty Detail PO)
            if ($header->referensi) {
                \App\Models\SuratPesananHeaderModel::where('no_surat_pesanan', $header->referensi)
                    ->update(['status_penerimaan' => $statusPO]);
            }

            // $header->update(['status' => 'sukses']);
            $header->update([
                'status'        => 'sukses',
                'diterima_dari' => $request->diterima_dari,
                'diterima_oleh' => $request->diterima_oleh,
                'tanggal' => $request->tanggal,
                'supplier_id'   => $request->supplier_id, // Tambahkan ini jika supplier boleh diubah saat approve
            ]);

            DB::commit();
            return redirect()->route('v2sparepartinmultiple.index')->with('success', 'Penerimaan Berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd("ERROR: " . $e->getMessage(), "LINE: " . $e->getLine());
        }
    }

    // public function approve(Request $request, $id)
    // {

    //     // dd([
    //     //     'Tujuan_Action' => $request->action_type,
    //     //     'ID_Produk' => $request->product,
    //     //     'Sisa_Yang_Harus_Ada' => $request->demand,
    //     //     'Kenyataan_Datang' => $request->qty_datang,
    //     //     'PO_Number' => $request->po_numbers
    //     // ]);

    //     // 1. TAMBAHKAN VALIDASI DI SINI
    //     $request->validate([
    //         'diterima_oleh' => 'required|string|max:255',
    //         'diterima_dari' => 'required|string|max:255',
    //     ], [
    //         'diterima_oleh.required' => 'Nama penerima wajib diisi!',
    //         'diterima_dari.required' => 'Nama pengirim wajib diisi!',
    //     ]);

    //     $header = StockInHeader::findOrFail($id);
    //     if ($header->status !== 'Draft') return back();

    //     DB::beginTransaction();
    //     try {
    //         $action = $request->input('action_type');
    //         $backorderNeeded = false;
    //         $backorderItems = [];

    //         foreach ($request->product as $i => $spare_part_id) {
    //             $qtyDatang = (int)$request->qty_datang[$i];
    //             $qtyPesanAsli = (int)$request->demand[$i]; // Ini angka pesan (misal: 10)

    //             $detail = StockTransactionModel::where('stock_in_header_id', $header->id)
    //                 ->where('spare_part_id', $spare_part_id)
    //                 ->first();

    //             if ($detail) {
    //                 // Cek apakah ada barang yang kurang untuk potensi Backorder
    //                 if ($qtyDatang < $qtyPesanAsli) {
    //                     $backorderNeeded = true;
    //                     $selisih = $qtyPesanAsli - $qtyDatang;

    //                     if ($selisih > 0) {
    //                         $backorderItems[] = [
    //                             'spare_part_id' => $spare_part_id,
    //                             'quantity'      => $selisih,
    //                             'price'         => $detail->price,
    //                         ];
    //                     }
    //                 }

    //                 // UPDATE: Simpan yang BENAR-BENAR DATANG ke kolom quantity
    //                 // Qty Pesan asli (10) tidak hilang karena masih ada di tabel Surat Pesanan (PO)
    //                 $detail->update([
    //                     'quantity' => $qtyDatang,
    //                     'status'   => 'sukses',
    //                     'user'     => $request->diterima_oleh
    //                 ]);

    //                 // Tambah stok master hanya sesuai yang datang
    //                 if ($qtyDatang > 0) {
    //                     $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);
    //                     $sparePart->increment('stock', $qtyDatang);
    //                 }
    //             }
    //         }

    //         // --- STEP 2: TARUH LOGIKA BACKORDER DI SINI ---
    //         if ($backorderNeeded && $action === 'backorder' && count($backorderItems) > 0) {

    //             // Buat Header baru
    //             $newHeader = StockInHeader::create([
    //                 'no_dokumen'    => $header->no_dokumen . '-BO',
    //                 'supplier_id'   => $header->supplier_id,
    //                 'referensi'     => $header->referensi,
    //                 'tanggal'       => now(),
    //                 'diterima_dari' => $header->diterima_dari,
    //                 'diterima_oleh' => $request->diterima_oleh,
    //                 'status'        => 'Draft',
    //                 'keterangan'    => 'Backorder dari ' . $header->no_dokumen,
    //                 'category_id'   => $header->category_id,
    //                 'location_id'   => $header->location_id,
    //             ]);

    //             // Buat Detail untuk sisa barang
    //             foreach ($backorderItems as $item) {
    //                 StockTransactionModel::create([
    //                     'stock_in_header_id' => $newHeader->id,
    //                     'spare_part_id'      => $item['spare_part_id'],
    //                     'type'               => 'in',
    //                     'quantity'           => $item['quantity'],
    //                     'price'              => $item['price'],
    //                     'status'             => 'Draft',
    //                     'user'               => $request->diterima_oleh,
    //                 ]);
    //             }
    //             $statusPO = 'terima sebagian';
    //         } else {
    //             $statusPO = 'closed';
    //         }

    //         // Update status di tabel Surat Pesanan (PO)
    //         if ($header->referensi) {
    //             \App\Models\SuratPesananHeaderModel::where('no_surat_pesanan', $header->referensi)
    //                 ->update(['status_penerimaan' => $statusPO]);
    //         }

    //         $header->update(['status' => 'sukses']);

    //         DB::commit();
    //         return redirect()->route('v2sparepartinmultiple.index')->with('success', 'Penerimaan Berhasil!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Munculkan error aslinya biar kita tau kenapa gagal save
    //         dd($e->getMessage(), $e->getLine(), $e->getFile());
    //     }
    // }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $header = StockInHeader::findOrFail($id);

            if ($header->status !== 'Proses') {
                return redirect()->route('v2sparepartinmultiple.index')
                    ->with('error', 'Hanya dokumen berstatus Proses yang bisa dibatalkan langsung!');
            }

            // 1. Deteksi apakah ini dokumen Backorder
            $isBackorder = str_contains($header->no_dokumen, '-BO');

            // 2. Update Status Header & Detail menjadi 'batal'
            $header->update([
                'status' => 'batal',
                'keterangan' => $request->alasan_batal
            ]);

            StockTransactionModel::where('stock_in_header_id', $header->id)
                ->update([
                    'status' => 'batal',
                    'keterangan' => 'Dibatalkan: ' . $request->alasan_batal
                ]);

            // 3. LOGIKA UPDATE STATUS PO
            if ($header->referensi) {
                // Tentukan status dan teks keterangan
                $statusPenerimaanBaru = $isBackorder ? 'closed' : 'cancel';

                // Modifikasi teks keterangan di sini
                $teksBatal = $isBackorder ? 'Dibatalkan Sebagian' : 'Dibatalkan';

                \App\Models\SuratPesananHeaderModel::where('no_surat_pesanan', $header->referensi)
                    ->update([
                        'status_penerimaan' => $statusPenerimaanBaru,
                        // Hasilnya jadi: "Dibatalkan Sebagian (WH/IN/001-BO): alasan user"
                        'keterangan' => $teksBatal . ' (' . $header->no_dokumen . '): ' . $request->alasan_batal
                    ]);
            }

            // 3. LOGIKA UPDATE STATUS PO
            // if ($header->referensi) {
            //     // Tentukan status penerimaan berdasarkan jenis dokumen
            //     // Jika Backorder di-cancel -> 'closed'
            //     // Jika Dokumen Utama di-cancel -> 'cancel'
            //     $statusPenerimaanBaru = $isBackorder ? 'closed' : 'cancel';

            //     \App\Models\SuratPesananHeaderModel::where('no_surat_pesanan', $header->referensi)
            //         ->update([
            //             'status_penerimaan' => $statusPenerimaanBaru,
            //             'keterangan' => 'Dibatalkan (' . $header->no_dokumen . '): ' . $request->alasan_batal
            //         ]);
            // }

            DB::commit();

            $pesan = $isBackorder ? 'Backorder dibatalkan, Pesanan ditutup (Closed).' : 'Dokumen utama berhasil dibatalkan.';
            return redirect()->route('v2sparepartinmultiple.index')->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    // public function cancel(Request $request, $id)
    // {

    //     $request->validate([
    //         'alasan_batal' => 'required|string|max:255'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // 1. Cari data Header
    //         $header = StockInHeader::findOrFail($id);

    //         // PROTEKSI: Jika status sudah 'sukses' atau sudah 'cancel', hentikan.
    //         // Karena permintaan Anda hanya untuk status draft.
    //         if ($header->status !== 'Draft') {
    //             return redirect()->route('v2sparepartinmultiple.index')
    //                 ->with('error', 'Hanya dokumen berstatus Draft yang bisa dibatalkan langsung!');
    //         }

    //         // 2. Update Status Header menjadi 'cancel'
    //         $header->update([
    //             'status' => 'batal',
    //             'keterangan' => $request->alasan_batal
    //         ]);

    //         // 3. Update semua detail transaksi yang terkait dengan header ini menjadi 'cancel'
    //         // Kita gunakan update massal karena tidak ada manipulasi stok master
    //         StockTransactionModel::where('stock_in_header_id', $header->id)
    //             ->update([
    //                 'status' => 'batal',
    //                 'keterangan' => 'Dibatalkan: ' . $request->alasan_batal // Opsional: tulis juga di detail
    //             ]);

    //         if ($header->referensi) {
    //             \App\Models\SuratPesananHeaderModel::where('no_surat_pesanan', $header->referensi)
    //                 ->update([
    //                     'status_penerimaan' => 'cancel',
    //                     'keterangan' => $request->alasan_batal
    //                 ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('v2sparepartinmultiple.index')
    //             ->with('success', 'Dokumen draft berhasil dibatalkan.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
    //     }
    // }

    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([], 200);
        }

        $results = ListSparePartModel::whereNotNull('name')
            ->where('name', '!=', '')
            ->where('name', 'like', '%' . $q . '%')
            // Tambahkan filter ini untuk hanya mengambil yang Active
            ->whereHas('produkstatus', function ($query) {
                $query->where('name', 'Active');
            })
            ->limit(20)
            ->get();

        // $results = ListSparePartModel::whereNotNull('name')
        //     ->where('name', '!=', '')
        //     ->where('name', 'like', '%' . $q . '%')
        //     ->limit(20)
        //     ->get();

        $formatted = $results->map(function ($item) {
            return [
                'label' => $item->name,
                'value' => $item->name,
                'id'    => $item->id,   // tambahkan id
            ];
        });


        return response()->json($formatted);
    }

    public function show($id)
    {
        $transaction = StockInHeader::with('stockTransactions.sparePart')->findOrFail($id);
        $suppliers = SupplierModel::all();

        return view('dashboard.sparepartmasukv2.show', compact('transaction', 'suppliers'));
    }

    // Spare Part Out
    public function indexout(Request $request)
    {
        $query = StockOutHeader::with('user')->orderBy('updated_at', 'desc');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15);

        return view('dashboard.sparepartoutmultiple.listsparepartoutmultiple', compact('transactions'));
    }

    public function createout()
    {
        $tahun = now()->format('Y');

        // Ambil record terakhir tahun ini
        $last = StockOutHeader::whereYear('tanggal', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        // Ambil nomor urut terakhir
        $lastNumber = 0;
        if ($last && preg_match('/(\d{3})$/', $last->no_dokumen, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        // Generate nomor baru
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $noDokumen = "WH/OUT/{$tahun}/{$nextNumber}";

        $locations = LocationsModel::all();
        $categories = CategoryModel::all();

        return view('dashboard.sparepartoutmultiple.create', compact('noDokumen', 'locations', 'categories'));
    }

    public function storeout(Request $request)
    {
        // Validasi input dasar dulu
        $request->validate([
            'diminta_oleh'  => 'required|string|max:100',
            'product'       => 'required|array',
            'demand'        => 'required|array',
        ], [
            'diminta_oleh.required' => 'Form ini harus diisi',
        ]);

        $errors = [];

        // Loop cek stok semua produk
        foreach ($request->product as $i => $spare_part_id) {
            $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

            if (!$sparePart) {
                $errors["product.$i"] = "Spare part tidak ditemukan.";
                continue;
            }

            if ($request->demand[$i] > $sparePart->stock) {
                $errors["demand.$i"] = "Jumlah keluar melebihi stok yang tersedia (Stok: {$sparePart->stock}).";
            }
        }

        // Jika ada error stok, kembalikan dengan error sekaligus
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Jika valid, proses simpan transaksi stok keluar
        return DB::transaction(function () use ($request) {
            $header = StockOutHeader::create([
                'no_dokumen'    => $request->no_dokumen,
                'tanggal'       => $request->tanggal,
                'diminta_oleh'  => $request->diminta_oleh,
                'locations_id' => $request->locations_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'status' => 'sukses'
            ]);

            foreach ($request->product as $i => $spare_part_id) {
                $sparePart = \App\Models\ListSparePartModel::find($spare_part_id);

                StockTransactionModel::create([
                    'stock_out_header_id' => $header->id,
                    'spare_part_id'       => $spare_part_id,
                    'type'                => 'out',
                    'quantity'            => $request->demand[$i],
                    'price'               => $sparePart->price, // ambil harga dari master
                    'user'                => $request->diminta_oleh,
                    'status' => 'sukses',
                ]);
            }

            return redirect()->route('sparepartoutmultiple.index')->with('success', 'Stok keluar berhasil dicatat.');
        });
    }

    // public function storeout(Request $request)
    // {

    //     // Validasi input
    //     $validated = $request->validate([
    //         'diminta_oleh'  => 'required|string|max:100',
    //     ], [
    //         'diminta_oleh' => 'Form ini harus diisi',
    //     ]);

    //     return DB::transaction(function () use ($request) {
    //         $header = StockOutHeader::create([
    //             'no_dokumen'    => $request->no_dokumen,
    //             'tanggal'       => $request->tanggal,
    //             'diminta_oleh' => $request->diminta_oleh
    //         ]);

    //         foreach ($request->product as $i => $spare_part_id) {
    //             StockTransactionModel::create([
    //                 'stock_out_header_id' => $header->id,
    //                 'spare_part_id'      => $spare_part_id,
    //                 'type'               => 'out',
    //                 'quantity'           => $request->demand[$i],
    //                 'user'               => $request->diminta_oleh
    //             ]);
    //         }

    //         return redirect()->route('sparepartoutmultiple.index')->with('success', 'Stok masuk berhasil dicatat.');
    //     });
    // }

    public function showout($id)
    {
        $transaction = StockOutHeader::with('stockTransactions.sparePart')->findOrFail($id);

        return view('dashboard.sparepartoutmultiple.show', compact('transaction'));
    }

    public function batalmasuk(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|min:5'
        ]);

        $transaction = StockInHeader::findOrFail($id);

        if ($transaction->status !== 'sukses') {
            return back()->with('error', 'Transaksi tidak bisa dibatalkan');
        }

        $transaction->update([
            'status'     => 'batal',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('sparepartinmultiple.index')
            ->with('success', 'Transaksi berhasil dibatalkan');
    }

    public function batalkeluar(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|min:5'
        ]);

        $transaction = StockOutHeader::findOrFail($id);

        if ($transaction->status !== 'sukses') {
            return back()->with('error', 'Transaksi tidak bisa dibatalkan');
        }

        $transaction->update([
            'status'     => 'batal',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('sparepartoutmultiple.index')
            ->with('success', 'Transaksi berhasil dibatalkan');
    }

    public function searchsp(Request $request)
    {
        $q = $request->q;

        return SuratPesananHeaderModel::where('status', 'approved')
            ->where('no_surat_pesanan', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'label' => $item->no_surat_pesanan,
                    'value' => $item->no_surat_pesanan,
                ];
            });
    }

    public function retur(Request $request, $id)
    {
        $request->validate([
            'sparepart_id' => 'required|array',
            'qty_retur'    => 'required|array',
            'alasan_retur' => 'required|string|max:255',
        ]);

        $stockIn = StockInHeader::findOrFail($id);

        try {
            DB::beginTransaction();

            // --- 1. GENERATE NOMOR DOKUMEN (Logika Global yang sudah kita buat) ---
            $tahun = now()->format('Y');
            $prefix = "WH/OUT/{$tahun}/";

            $lastAnyDoc = StockOutHeader::where('no_dokumen', 'LIKE', $prefix . '%')
                ->orderBy('no_dokumen', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastAnyDoc) {
                $parts = explode('/', $lastAnyDoc->no_dokumen);
                if (isset($parts[3])) {
                    $nextNumber = (int) $parts[3] + 1;
                }
            }

            $lastReturDoc = StockOutHeader::where('no_dokumen', 'LIKE', '%/RET-%')
                ->orderBy('no_dokumen', 'desc')
                ->first();

            $nextReturNumber = 1;
            if ($lastReturDoc) {
                $lastRetParts = explode('RET-', $lastReturDoc->no_dokumen);
                $nextReturNumber = (int) end($lastRetParts) + 1;
            }

            $noDokOut = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/RET-' . str_pad($nextReturNumber, 2, '0', STR_PAD_LEFT);

            // --- 2. VALIDASI SISA BARANG & PROSES FOREACH ---
            $hasRetur = false;
            $detailsToCreate = []; // Temporary storage untuk data yang valid

            foreach ($request->sparepart_id as $key => $sparepart_id) {
                $qtyInput = (int) $request->qty_retur[$key];

                if ($qtyInput > 0) {
                    // Cari berapa jumlah awal barang ini di StockIn ini
                    $qtyAwal = StockTransactionModel::where('stock_in_header_id', $id)
                        ->where('spare_part_id', $sparepart_id)
                        ->where('type', 'in')
                        ->sum('quantity');

                    // Cari berapa yang sudah diretur sebelumnya untuk referensi dokumen ini
                    $sudahRetur = StockTransactionModel::where('spare_part_id', $sparepart_id)
                        ->where('keterangan', 'LIKE', '%Ref: ' . $stockIn->no_dokumen . '%')
                        ->where('type', 'out')
                        ->sum('quantity');

                    $sisaBisaRetur = $qtyAwal - $sudahRetur;

                    // Cek apakah input melebihi sisa
                    if ($qtyInput > $sisaBisaRetur) {
                        throw new \Exception("Jumlah retur untuk salah satu barang melebihi sisa yang ada.");
                    }

                    $hasRetur = true;

                    // Simpan data untuk diproses setelah header dibuat
                    $detailsToCreate[] = [
                        'id' => $sparepart_id,
                        'qty' => $qtyInput
                    ];
                }
            }

            if (!$hasRetur) {
                return redirect()->back()->with('error', 'Isi jumlah retur minimal 1 barang.');
            }

            // --- 3. EKSEKUSI SIMPAN DATA ---
            $stockOut = StockOutHeader::create([
                'no_dokumen'     => $noDokOut,
                'diminta_oleh'   => 'Retur Barang',
                'tanggal'        => now(),
                'locations_id'   => $stockIn->location_id,
                'category_id'    => $stockIn->category_id,
                'subcategory_id' => $stockIn->subcategory_id,
                'status'         => 'sukses',
                'keterangan'     => 'RETUR (Ref: ' . $stockIn->no_dokumen . ') ' . $request->alasan_retur,
            ]);

            foreach ($detailsToCreate as $detail) {
                $sparepart = ListSparePartModel::findOrFail($detail['id']);
                $sparepart->decrement('stock', $detail['qty']);

                StockTransactionModel::create([
                    'spare_part_id'       => $detail['id'],
                    'type'                => 'out',
                    'quantity'            => $detail['qty'],
                    'user'                => 'Retur Barang',
                    'stock_in_header_id'  => 0,
                    'stock_out_header_id' => $stockOut->id,
                    'price'               => $sparepart->price ?? 0,
                    'status'              => 'sukses',
                    'keterangan'          => 'Retur Barang (' . $noDokOut . ') Ref: ' . $stockIn->no_dokumen,
                ]);
            }

            DB::commit();
            return redirect()->route('v2sparepartinmultiple.index',  $stockIn->id)
                ->with('success', 'Retur berhasil diproses');
            // return redirect()->back()->with('success', "Retur Berhasil! Dokumen $noDokOut telah dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal Simpan: ' . $e->getMessage());
        }
    }
    
}
