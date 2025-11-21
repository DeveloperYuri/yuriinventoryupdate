<?php

namespace App\Http\Controllers;

use App\Models\AtkModel;
use App\Models\LocationsModel;
use App\Models\SuratpesananatkheaderModel;
use App\Models\SuratpesanandetailatkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratpesananatkController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = SuratpesananatkheaderModel::getRecord($request);

        return view('dashboard.atk.suratpesananatk.index', $data);
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
        $last = SuratpesananatkheaderModel::whereYear('created_at', $tahun)
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

        $locations = LocationsModel::all();


        return view('dashboard.atk.suratpesananatk.create', compact('noDokumen', 'locations'));
    }

    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'dibuat_oleh'          => 'required|string|max:100',
            'locations_id'  => 'required|integer|exists:locations,id',
        ], [
            'dibuat_oleh.required'          => 'Form ini harus diisi',
            'locations_id.required'  => 'Lokasi wajib dipilih',
        ]);

        // Simpan semua dalam transaksi
        return DB::transaction(function () use ($request) {

            // Simpan header surat pesanan
            $header = SuratpesananatkheaderModel::create([
                'no_surat_pesanan' => $request->no_surat_pesanan,
                'dibuat_oleh'             => $request->dibuat_oleh,
                'locations_id'     => $request->locations_id
            ]);

            // Loop semua spare part untuk simpan detail
            foreach ($request->product as $i => $atk_id) {
                // Ambil nama sparepart dari master (optional)
                $atk = \App\Models\AtkModel::find($atk_id);

                SuratpesanandetailatkModel::create([
                    'surat_pesanan_atk_header_id' => $header->id,   // wajib ada
                    'atk_id'           => $atk_id,
                    'qty'                     => $request->demand[$i] ?? 0,
                    'stock'                   => $request->stock[$i] ?? 0,  // ambil dari form
                ]);
            }

            return redirect()->route('suratpesanan-atk.index')
                ->with('success', 'Surat pesanan ATK Berhasil Dibuat.');
        });
    }

    public function edit($id)
    {
        $transaction = SuratpesananatkheaderModel::with([
            'details',
            'location',
        ])->findOrFail($id);

        $locations = LocationsModel::all();

        $spareparts = AtkModel::orderBy('name')->get();

        return view('dashboard.atk.suratpesananatk.edit', compact('transaction', 'spareparts', 'locations'));
    }

    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'name'           => 'required|string|max:100',
        //     'locations_id'   => 'required|integer|exists:locations,id',
        //     'category_id'    => 'required|integer|exists:category,id',
        //     'subcategory_id' => 'required|integer|exists:subcategory,id',
        // ]);

        return DB::transaction(function () use ($request, $id) {
            // 🔹 Update header
            $header = SuratpesananatkheaderModel::findOrFail($id);
            $header->update([
                'dibuat_oleh'           => $request->dibuat_oleh,
                'locations_id'   => $request->locations_id,
            ]);

            // 🔹 Ambil semua ID detail yang ada di request
            $detailIds = collect($request->details)->pluck('id')->filter()->toArray();

            // 🔹 Hapus detail yang tidak ada di request
            SuratpesanandetailatkModel::where('surat_pesanan_atk_header_id', $id)
                ->whereNotIn('id', $detailIds)
                ->delete();

            // 🔹 Update atau insert detail lama
            foreach ($request->details as $detailData) {
                if (!empty($detailData['id'])) {
                    // Update existing
                    $detail = SuratpesanandetailatkModel::findOrFail($detailData['id']);
                    $detail->update([
                        'atk_id' => $detailData['atk_id'],
                        'qty'           => $detailData['qty'],
                        'stock'         => $detailData['stock'] ?? $detail->stock,
                    ]);
                } else {
                    // Insert baru (kalau ada baris tambahan manual)
                    SuratpesanandetailatkModel::create([
                        'surat_pesanan_atk_header_id' => $id,
                        'atk_id'           => $detailData['atk_id'],
                        'qty'                     => $detailData['qty'],
                        'stock'                   => $detailData['stock'] ?? 0,
                    ]);
                }
            }

            return redirect()->route('suratpesanan-atk.index')
                ->with('success', 'Surat pesanan berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        $suratpesanan = SuratpesananatkheaderModel::findorFail($id);

        $suratpesanan->delete();

        return redirect()->route('suratpesanan-atk.index')->with('success', 'Surat pesanan berhasil dicatat.');
    }

    public function show($id)
    {

        $transaction = SuratpesananatkheaderModel::with('details')->find($id);

        return view('dashboard.atk.suratpesananatk.show', compact('transaction'));
    }

    public function printPdf($id)
    {
        $transaction = SuratpesananatkheaderModel::with('details.atk')->findOrFail($id);

        $pdf = Pdf::loadView('dashboard.atk.suratpesananatk.laporanpdf', compact('transaction'));

        return $pdf->stream(); // buka di browser
    }

    public function getStock($id)
    {
        $sparePart = AtkModel::find($id);
        return response()->json([
            'stock' => $sparePart ? $sparePart->stock : 0
        ]);
    }

    public function submit($id)
    {
        $header = SuratpesananatkheaderModel::findOrFail($id);
        $header->status = 'onprogress';
        $header->save();

        return back()->with('success', 'Surat pesanan diajukan untuk approval.');
    }

    public function approve($id)
    {
        $header = SuratpesananatkheaderModel::findOrFail($id);
        $header->status = 'approve';
        $header->save();

        return back()->with('success', 'Surat pesanan disetujui.');
    }

    public function reject($id)
    {
        $header = SuratpesananatkheaderModel::findOrFail($id);
        $header->status = 'reject';
        $header->save();

        return back()->with('success', 'Surat pesanan ditolak.');
    }
}
