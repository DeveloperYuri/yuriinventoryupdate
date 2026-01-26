<?php

namespace App\Http\Controllers;

use App\Models\AssetitModel;
use App\Models\LocationsModel;
use App\Models\PerbaikansparepartModel;
use App\Models\RiwayatperbaikanassetitModel;
use App\Models\SparepartitModel;
use App\Models\SparepartittrasactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class RiwayatperbaikanassetitController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = RiwayatperbaikanassetitModel::getRecord($request);

        return view('dashboard.assetit.riwayatperbaikanassetit.index', compact('getRecord'));
    }

    public function create()
    {
        $assets    = AssetitModel::select('nomer_asset')->get();
        $locations = LocationsModel::all();
        $spareparts = SparepartitModel::orderBy('name')->get();


        return view('dashboard.assetit.riwayatperbaikanassetit.create', compact('assets', 'locations', 'spareparts'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'user' => 'required|string',
            'locations_id' => 'required|integer',
            'kerusakan' => 'required|string',
            // 'perbaikan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|string',
        ], [
            'image.required'   => 'File gambar harus diisi',
            'image.image'   => 'File harus berupa gambar',
            'image.mimes'   => 'File harus JPG, JPEG, PNG, atau GIF',
            'image.max'     => 'Ukuran file maksimal 5MB',
            'nomer_asset' => 'Nomor Asset IT wajib diisi',
            'nama' => 'Nama Asset IT wajib diisi',
            'user' => 'User Asset IT wajib diisi',
            'locations_id' => 'Lokasi Asset IT wajib diisi',
            'kerusakan' => 'Kerusakan Asset IT wajib diisi',
            // 'perbaikan' => 'Perbaikan Asset IT wajib diisi',
            'tanggal_mulai' => 'Tanggal mulai perbaikan wajib diisi',
            'status' => 'Status Perbaikan Asset IT wajib diisi',
        ]);

        $data = $request->only('nomer_asset', 'image', 'nama', 'user', 'locations_id', 'kerusakan', 'perbaikan', 'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan');

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        $perbaikan = RiwayatperbaikanassetitModel::create($data);

        if ($request->spareparts) {
            foreach ($request->spareparts as $sp) {

                if (empty($sp['sparepart_id'])) {
                    continue;
                }

                $qty = $sp['qty'] ?? 1;

                // 1️⃣ AMBIL SPARE PART + LOCK
                $sparepart = SparepartitModel::lockForUpdate()
                    ->findOrFail($sp['sparepart_id']);

                // STOK KOSONG
                if ($sparepart->stock <= 0) {
                    throw ValidationException::withMessages([
                        'spareparts' =>
                        "Stok spare part IT '{$sparepart->name}' kosong",
                    ]);
                }

                // STOK TIDAK CUKUP
                if ($sparepart->stock < $qty) {
                    throw ValidationException::withMessages([
                        'spareparts' =>
                        "Stok spare part IT '{$sparepart->name}' tidak mencukupi (tersedia {$sparepart->stock})",
                    ]);
                }

                PerbaikansparepartModel::create([
                    'perbaikan_id'   => $perbaikan->id,
                    'sparepartit_id' => $sp['sparepart_id'],
                    'qty'            => $qty,
                ]);

                // 2️⃣ CATAT RIWAYAT SPARE PART KELUAR (OUT)
                SparepartittrasactionModel::create([
                    'sparepartit_id' => $sp['sparepart_id'],
                    'type'           => 'out',
                    'quantity'       => $qty,
                    'user'           => $request->user,
                    'status'         => 'sukses',
                    'keterangan'     => 'Digunakan untuk perbaikan asset: ' . $request->nomer_asset,
                ]);
            }
        }

        AssetitModel::where('nomer_asset', $request->nomer_asset)
            ->update([
                'status' => $request->status === 'Sedang Perbaikan'
                    ? 'Sedang Perbaikan'
                    : 'DiPakai'
            ]);


        return redirect()->route('perbaikanasset-it.index')->with('success', 'Data perbaikan asset IT berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::with('spareparts.sparepart')
            ->findOrFail($id);
        $locations = LocationsModel::all();
        $spareparts = SparepartitModel::orderBy('name')->get();

        return view('dashboard.assetit.riwayatperbaikanassetit.edit', compact('riwayatperbaikanassetit', 'locations', 'spareparts'));
    }

    public function update(Request $request, $id)
    {
        // 1️⃣ validasi form dasar
        $request->validate([
            'nomer_asset' => 'required|string',
            'nama' => 'required|string',
            'user' => 'required|string',
            'locations_id' => 'required|integer',
            'kerusakan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|string',
        ]);

        // 2️⃣ VALIDASI STOK → DI SINI (SEBELUM TRANSACTION)
        if ($request->spareparts) {

            foreach ($request->spareparts as $index => $sp) {

                if (empty($sp['sparepart_id'])) continue;

                $qtyBaru = $sp['qty'] ?? 1;

                $sparepart = SparepartitModel::find($sp['sparepart_id']);

                if (!$sparepart) {
                    return back()
                        ->withInput()
                        ->with('error', 'Spare part tidak ditemukan');
                }

                // ⚠️ KHUSUS UPDATE:
                // stok lama SUDAH dibalikin → jadi stok sekarang valid
                if ($qtyBaru > $sparepart->stock) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "Stok spare part '{$sparepart->name}' tidak mencukupi. Sisa: {$sparepart->stock}"
                        );
                }
            }
        }

        // 3️⃣ BARU TRANSACTION
        DB::transaction(function () use ($request, $id) {

            $perbaikan = RiwayatperbaikanassetitModel::with('spareparts')->findOrFail($id);

            // balikin stok lama
            foreach ($perbaikan->spareparts as $old) {
                SparepartitModel::where('id', $old->sparepartit_id)
                    ->increment('stock', $old->qty);
            }

            PerbaikansparepartModel::where('perbaikan_id', $perbaikan->id)->delete();

            // update header
            $perbaikan->update([
                'nomer_asset'   => $request->nomer_asset,
                'nama'          => $request->nama,
                'user'          => $request->user,
                'locations_id'  => $request->locations_id,
                'kerusakan'     => $request->kerusakan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status'        => $request->status,
                'keterangan'    => $request->keterangan,
            ]);

            // simpan sparepart baru + potong stok
            if ($request->spareparts) {
                foreach ($request->spareparts as $sp) {

                    if (empty($sp['sparepart_id'])) continue;

                    $qtyBaru = $sp['qty'] ?? 1;

                    // Update transaction lama sesuai sparepart + asset
                    SparepartittrasactionModel::where('sparepartit_id', $sp['sparepart_id'])
                        ->where('type', 'out')
                        ->where('keterangan', 'like', '%' . $perbaikan->nomer_asset . '%')
                        ->update([
                            'quantity'   => $qtyBaru,
                            'user'       => $request->user,
                            'status'     => 'sukses',
                            'updated_at' => now(),
                        ]);

                    PerbaikansparepartModel::create([
                        'perbaikan_id'   => $perbaikan->id,
                        'sparepartit_id' => $sp['sparepart_id'],
                        'qty'            => $qtyBaru,
                    ]);

                    SparepartitModel::where('id', $sp['sparepart_id'])
                        ->decrement('stock', $qtyBaru);
                }
            }
        });

        return redirect()
            ->route('perbaikanasset-it.index')
            ->with('success', 'Data Perbaikan Asset IT berhasil diperbarui.');
    }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nomer_asset' => 'required|string',
    //         'nama' => 'required|string',
    //         'user' => 'required|string',
    //         'locations_id' => 'required|integer',
    //         'kerusakan' => 'required|string',
    //         // 'perbaikan' => 'required|string',
    //         'tanggal_mulai' => 'required|date',
    //         'status' => 'required|string',
    //     ]);

    //     DB::transaction(function () use ($request, $id) {

    //         /** ===============================
    //          * 1️⃣ AMBIL DATA PERBAIKAN + SPAREPART
    //          * =============================== */
    //         $perbaikan = RiwayatperbaikanassetitModel::with('spareparts')
    //             ->findOrFail($id);

    //         /** ===============================
    //          * 2️⃣ BALIKIN STOK SPAREPART LAMA
    //          * =============================== */
    //         foreach ($perbaikan->spareparts as $old) {
    //             SparepartitModel::where('id', $old->sparepartit_id)
    //                 ->increment('stock', $old->qty);
    //         }

    //         /** ===============================
    //          * 3️⃣ HAPUS RELASI SPAREPART LAMA
    //          * =============================== */
    //         PerbaikansparepartModel::where('perbaikan_id', $perbaikan->id)->delete();

    //         /** ===============================
    //          * 4️⃣ UPDATE HEADER PERBAIKAN
    //          * =============================== */
    //         $perbaikan->update([
    //             'nomer_asset'      => $request->nomer_asset,
    //             'nama'             => $request->nama,
    //             'user'             => $request->user,
    //             'locations_id'     => $request->locations_id,
    //             'kerusakan'        => $request->kerusakan,
    //             'perbaikan'        => $request->perbaikan,
    //             'tanggal_mulai'    => $request->tanggal_mulai,
    //             'tanggal_selesai'  => $request->tanggal_selesai,
    //             'status'           => $request->status,
    //             'keterangan'       => $request->keterangan,
    //         ]);

    //         /** ===============================
    //          * 5️⃣ UPDATE FOTO (OPSIONAL)
    //          * =============================== */
    //         if ($request->hasFile('image')) {
    //             if ($perbaikan->image && file_exists(public_path('images/' . $perbaikan->image))) {
    //                 unlink(public_path('images/' . $perbaikan->image));
    //             }

    //             $imageName = time() . '.' . $request->image->extension();
    //             $request->image->move(public_path('images'), $imageName);
    //             $perbaikan->image = $imageName;
    //             $perbaikan->save();
    //         }

    //         /** ===============================
    //          * 6️⃣ SIMPAN SPAREPART BARU + KURANGI STOK
    //          * =============================== */

    //         if ($request->spareparts) {

    //             foreach ($request->spareparts as $sp) {

    //                 if (empty($sp['sparepart_id'])) continue;

    //                 $qtyBaru = (int) ($sp['qty'] ?? 1);

    //                 // 🔒 lock row sparepart (ANTI RACE CONDITION)
    //                 $sparepart = SparepartitModel::where('id', $sp['sparepart_id'])
    //                     ->lockForUpdate()
    //                     ->first();

    //                 if (!$sparepart) {
    //                     throw new \Exception('Spare part tidak ditemukan');
    //                 }

    //                 /** ===============================
    //                  * VALIDASI STOK (KHUSUS UPDATE)
    //                  * =============================== */
    //                 // karena stok lama sudah dikembalikan di step 2,
    //                 // maka stock saat ini = stok valid
    //                 if ($qtyBaru > $sparepart->stock) {
    //                     throw new \Exception(
    //                         "Stok spare part '{$sparepart->name}' tidak mencukupi. Sisa: {$sparepart->stock}"
    //                     );
    //                 }

    //                 /** ===============================
    //                  * SIMPAN RELASI
    //                  * =============================== */
    //                 PerbaikansparepartModel::create([
    //                     'perbaikan_id'   => $perbaikan->id,
    //                     'sparepartit_id' => $sp['sparepart_id'],
    //                     'qty'            => $qtyBaru,
    //                 ]);

    //                 /** ===============================
    //                  * UPDATE TRANSACTION LAMA
    //                  * =============================== */
    //                 SparepartittrasactionModel::where('sparepartit_id', $sp['sparepart_id'])
    //                     ->where('type', 'out')
    //                     ->where('keterangan', 'like', '%' . $perbaikan->nomer_asset . '%')
    //                     ->update([
    //                         'quantity'   => $qtyBaru,
    //                         'user'       => $request->user,
    //                         'status'     => 'sukses',
    //                         'updated_at' => now(),
    //                     ]);

    //                 /** ===============================
    //                  * 🔻 KURANGI STOK
    //                  * =============================== */
    //                 $sparepart->decrement('stock', $qtyBaru);
    //             }
    //         }

    //         // if ($request->spareparts) {

    //         //     foreach ($request->spareparts as $sp) {

    //         //         if (empty($sp['sparepart_id'])) continue;

    //         //         $qtyBaru = $sp['qty'] ?? 1;

    //         //         // simpan relasi
    //         //         PerbaikansparepartModel::create([
    //         //             'perbaikan_id'   => $perbaikan->id,
    //         //             'sparepartit_id' => $sp['sparepart_id'],
    //         //             'qty'            => $qtyBaru,
    //         //         ]);

    //         //         /** ===============================
    //         //          * UPDATE TRANSACTION LAMA
    //         //          * =============================== */
    //         //         SparepartittrasactionModel::where('sparepartit_id', $sp['sparepart_id'])
    //         //             ->where('type', 'out')
    //         //             ->where('keterangan', 'like', '%' . $perbaikan->nomer_asset . '%')
    //         //             ->update([
    //         //                 'quantity' => $qtyBaru,
    //         //                 'user'     => $request->user,
    //         //                 'status'   => 'sukses',
    //         //                 'updated_at' => now(),
    //         //             ]);

    //         //         /** ===============================
    //         //          * 🔻 KURANGI STOK SESUAI QTY BARU
    //         //          * =============================== */
    //         //         SparepartitModel::where('id', $sp['sparepart_id'])
    //         //             ->decrement('stock', $qtyBaru);
    //         //     }
    //         // }

    //         /** ===============================
    //          * UPDATE STATUS ASSET
    //          * =============================== */
    //         $statusAsset = match (trim($request->status)) {
    //             'Sedang Perbaikan' => 'Sedang Perbaikan',
    //             'Selesai'          => 'Dipakai',
    //             default            => 'Tersedia',
    //         };

    //         AssetitModel::where('nomer_asset', trim($request->nomer_asset))
    //             ->update(['status' => $statusAsset]);
    //     });

    //     return redirect()
    //         ->route('perbaikanasset-it.index')
    //         ->with('success', 'Data Perbaikan Asset IT berhasil diperbarui.');
    // }

    public function show($id)
    {
        // $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::with([
            'spareparts.sparepart'
        ])->findOrFail($id);
        $locations = LocationsModel::all();

        return view('dashboard.assetit.riwayatperbaikanassetit.show', compact('riwayatperbaikanassetit', 'locations'));
    }

    public function destroy($id)
    {
        $riwayatperbaikanassetit = RiwayatperbaikanassetitModel::findOrFail($id);

        // Hapus gambar jika ada
        if ($riwayatperbaikanassetit->image && file_exists(public_path('images/' . $riwayatperbaikanassetit->image))) {
            unlink(public_path('images/' . $riwayatperbaikanassetit->image));
        }

        $riwayatperbaikanassetit->delete();

        return redirect()->route('perbaikanasset-it.index')->with('success', 'Riwayat Perbaikan Asset IT berhasil dihapus.');
    }

    public function ajaxDetail(Request $request)
    {
        $asset = AssetitModel::with(['user', 'location'])
            ->findOrFail($request->asset_id);

        return response()->json([
            'nama_asset' => $asset->nama_asset,
            'user' => $asset->user->name ?? '-',
            'lokasi' => $asset->location->name ?? '-',
            'status' => $asset->status,
        ]);
    }

    public function autocompleteSparepart(Request $request)
    {
        $q = $request->q;

        if (!$q) {
            return response()->json([]);
        }

        return SparepartitModel::where('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get(['id', 'name']);
    }
}
