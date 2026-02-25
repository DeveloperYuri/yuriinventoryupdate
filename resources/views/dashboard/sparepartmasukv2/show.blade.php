@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- <h2 class="mt-2">Detail Penerimaan Barang</h2> --}}

                            <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                                <h2 class="m-0">Detail Penerimaan Barang</h2>
                                <div class="d-flex align-items-center">
                                    @if ($transaction->status === 'Proses')
                                        <div class="alert alert-secondary py-2 px-3 mb-0">
                                            <i class="bi bi-info-circle me-1"></i> Status Dokumen:
                                            {{ ucfirst($transaction->status) }}
                                        </div>
                                    @elseif($transaction->status === 'sukses')
                                        <div class="alert alert-success py-2 px-3 mb-0">
                                            <i class="bi bi-info-circle me-1"></i> Status Dokumen:
                                            {{ ucfirst($transaction->status) }}
                                        </div>
                                    @else
                                        <div class="alert alert-danger py-2 px-3 mb-0">
                                            <i class="bi bi-info-circle me-1"></i> Status Dokumen:
                                            {{ ucfirst($transaction->status) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Pastikan Route mengarah ke fungsi storein di Controller --}}
                            <form id="approveForm" class="mt-4"
                                action="{{ route('v2sparepartinmultiple.approve', $transaction->id) }}" method="POST">
                                @csrf

                                {{-- Input Hidden untuk data pendukung --}}
                                <input type="hidden" name="supplier_id" value="{{ $transaction->supplier_id }}">
                                <input type="hidden" name="po_numbers" value="{{ $transaction->referensi }}">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No Dokumen</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="no_dokumen"
                                                    value="{{ $transaction->no_dokumen }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di terima dari</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('diterima_dari') is-invalid @enderror"
                                                    name="diterima_dari" {{-- Mengambil data lama jika ada, jika tidak ambil dari database --}}
                                                    value="{{ old('diterima_dari', $transaction->diterima_dari) }}"
                                                    {{-- Kunci input jika status sukses --}}
                                                    {{ $transaction->status === 'sukses' ? 'readonly' : '' }} required>
                                                @error('diterima_dari')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Supplier</label>
                                            <div class="col-sm-8">
                                                {{-- Kunci dropdown jika status sukses --}}
                                                <select name="supplier_id" class="form-control"
                                                    {{ $transaction->status === 'sukses' ? 'disabled' : '' }}>
                                                    <option value="">-- Pilih Supplier --</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{-- Cek old data atau data dari database --}}
                                                            {{ old('supplier_id', $transaction->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                {{-- Trick: Jika select disabled, datanya tidak akan terkirim saat submit. 
             Tambahkan hidden input agar datanya tetap aman jika status sukses --}}
                                                @if ($transaction->status === 'sukses')
                                                    <input type="hidden" name="supplier_id"
                                                        value="{{ $transaction->supplier_id }}">
                                                @endif
                                            </div>
                                        </div>
                                        {{-- <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di terima dari</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('diterima_dari') is-invalid @enderror"
                                                    name="diterima_dari"
                                                    value="{{ old('diterima_dari', $transaction->diterima_dari) }}"
                                                    required>
                                                @error('diterima_dari')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Supplier</label>
                                            <div class="col-sm-8">
                                                <select name="supplier_id" class="form-control">
                                                    <option value="">-- Pilih Supplier --</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                    </div>

                                    <div class="col-md-6">
                                        {{-- <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Tanggal</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="tanggal"
                                                    value="{{ $transaction->tanggal_display }}">
                                            </div>
                                        </div> --}}
                                        {{-- <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Tanggal</label>
                                            <div class="col-sm-8">
                                                <input id="tanggalMulai" name="tanggal" type="text" class="form-control"
                                                    placeholder="Pilih tanggal..." autocomplete="off"
                                                    value="{{ now()->format('Y-m-d') }}">
                                                <input type="hidden" name="tanggal" id="tanggalHidden"
                                                    value="{{ now()->format('Y-m-d') }}">
                                            </div>
                                        </div> --}}

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Tanggal</label>
                                            <div class="col-sm-8">
                                                <input id="tanggalMulai" name="tanggal" type="text" class="form-control"
                                                    placeholder="Pilih tanggal..." autocomplete="off" {{-- Jika $header->tanggal ada isinya, pakai itu. Jika null, pakai hari ini --}}
                                                    value="{{ $transaction->tanggal ? \Carbon\Carbon::parse($transaction->tanggal)->format('Y-m-d') : now()->format('Y-m-d') }}">

                                                <input type="hidden" name="tanggal" id="tanggalHidden"
                                                    value="{{ $transaction->tanggal ? \Carbon\Carbon::parse($transaction->tanggal)->format('Y-m-d') : now()->format('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di terima oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('diterima_oleh') is-invalid @enderror"
                                                    name="diterima_oleh"
                                                    value="{{ old('diterima_oleh', $transaction->diterima_oleh ?? Auth::user()->name) }}"
                                                    required>
                                                {{-- <input type="text"
                                                    class="form-control @error('diterima_oleh') is-invalid @enderror"
                                                    name="diterima_oleh"
                                                    value="{{ old('diterima_oleh', Auth::user()->name ?? '') }}" required> --}}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No. SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"
                                                    value="{{ $transaction->referensi ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content mt-4" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Spare Part</th>
                                                    <th>Qty Pesan</th> {{-- Ini akan tetap jadi angka pesanan asli --}}
                                                    <th>Qty Datang</th> {{-- Ini yang akan diupdate ke DB --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction->stockTransactions as $item)
                                                    <tr>
                                                        {{-- Kolom 1: Nama Produk --}}
                                                        <td>
                                                            {{ $item->sparePart->name ?? '-' }}
                                                            <input type="hidden" name="product[]"
                                                                value="{{ $item->spare_part_id }}">
                                                        </td>

                                                        {{-- Kolom 2: Qty Pesanan --}}
                                                        <td>
                                                            @php
                                                                // 1. Ambil Qty asli dari PO (untuk TAMPILAN saja)
                                                                $qtyPesanAsli = \App\Models\SuratPesananDetailModel::whereHas(
                                                                    'header',
                                                                    function ($q) use ($transaction) {
                                                                        $q->where(
                                                                            'no_surat_pesanan',
                                                                            $transaction->referensi,
                                                                        );
                                                                    },
                                                                )
                                                                    ->where('spare_part_id', $item->spare_part_id)
                                                                    ->value('qty_kurang');

                                                                // 2. Ambil Qty sisa transaksi saat ini (untuk LOGIC CONTROLLER)
                                                                $qtySisaTransaksi = (int) $item->quantity;
                                                            @endphp

                                                            {{-- TAMPILAN: User lihat jatah asli PO --}}
                                                            <strong>{{ (int) ($qtyPesanAsli ?? $qtySisaTransaksi) }}</strong>

                                                            {{-- HIDDEN: Controller terima jatah sisa transaksi agar tidak error --}}
                                                            <input type="hidden" name="demand[]"
                                                                value="{{ $qtySisaTransaksi }}">
                                                        </td>

                                                        {{-- Kolom 2: Qty Pesanan --}}
                                                        {{-- <td>
                                                            @php
                                                                $qtyAwal = (int) $item->quantity;
                                                            @endphp

                                                            <strong>{{ $qtyAwal }}</strong>
                                                            <input type="hidden" name="demand[]"
                                                                value="{{ $qtyAwal }}">
                                                        </td> --}}

                                                        {{-- Kolom 3: Qty Datang (Input) --}}
                                                        <td>
                                                            @php
                                                                $qtyAwal = (int) $item->quantity;
                                                            @endphp
                                                            <div class="input-group">
                                                                <input type="number" name="qty_datang[]"
                                                                    class="form-control text-center border-primary qty-input"
                                                                    value="{{ $qtyAwal }}" min="0"
                                                                    oninput="validateQty(this, {{ $qtyAwal }})"
                                                                    {{ $transaction->status !== 'Proses' ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                            {{-- Label kecil untuk info sisa (gantung) secara visual --}}
                                                            @if ($transaction->status == 'Proses')
                                                                <small class="text-muted">Sisa gantung: <span
                                                                        class="sisa-gantung">0</span></small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- <div class="tab-content mt-4" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Spare Part</th>
                                                    <th>Qty Pesan </th>
                                                    <th>Qty Datang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction->stockTransactions as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->sparePart->name ?? '-' }}
                                                            <input type="hidden" name="product[]"
                                                                value="{{ $item->spare_part_id }}">
                                                        </td>
                                                        <td>
                                                            {{ $item->quantity }}
                                                            <input type="hidden" name="demand[]"
                                                                value="{{ $item->quantity }}">
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" name="qty_datang[]"
                                                                    class="form-control text-center border-primary qty-input"
                                                                    value="{{ $item->quantity }}" min="0"
                                                                    max="{{ $item->quantity }}"
                                                                    oninput="validateQty(this, {{ $item->quantity }})"
                                                                    {{ $transaction->status !== 'Draft' ? 'readonly' : '' }}
                                                                    required>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div> --}}
                            </form>

                            {{-- BAGIAN ACTION BUTTONS --}}
                            <div class="mt-4 d-flex gap-2">
                                @if ($transaction->status === 'Proses')
                                    {{-- Tombol untuk submit Form Approve di atas --}}
                                    {{-- <button type="submit" form="approveForm" class="btn btn-success">
                                        <i class="bi bi-check-lg"></i> Terima
                                    </button> --}}
                                    <button type="button" onclick="checkBackorder()" class="btn btn-success">
                                        <i class="bi bi-check-lg"></i> Terima
                                    </button>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#cancelModal">
                                        <i class="bi bi-x-lg"></i> Batalkan
                                    </button>

                                    <div class="modal fade" id="cancelModal" tabindex="-1"
                                        aria-labelledby="cancelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form
                                                    action="{{ route('v2sparepartinmultiple.cancel', $transaction->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin membatalkan dokumen
                                                            <strong>{{ $transaction->no_dokumen }}</strong>?
                                                        </p>
                                                        <div class="form-group">
                                                            <label for="alasan_batal" class="mb-2">Alasan
                                                                Pembatalan:</label>
                                                            <textarea name="alasan_batal" id="alasan_batal" class="form-control" rows="3"
                                                                placeholder="Contoh: Salah input quantity atau barang rusak saat diterima" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-danger">Ya, Batalkan
                                                            Dokumen</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- @if ($transaction->status === 'sukses')
                                    <button type="button" class="btn btn-warning text-dark" data-bs-toggle="modal"
                                        data-bs-target="#returModal">
                                        <i class="bi bi-arrow-counterclockwise"></i> Retur Barang
                                    </button>
                                @endif --}}

                                @php
                                    // 1. Hitung total barang yang DITERIMA di dokumen ini
                                    $totalDiterima = $transaction->stockTransactions->sum('quantity');

                                    // 2. Hitung total barang yang SUDAH DIRETUR dari dokumen ini
                                    // Kita cari transaksi 'out' yang merujuk ke nomor dokumen ini
                                    $totalSudahRetur = \App\Models\StockTransactionModel::where(
                                        'keterangan',
                                        'LIKE',
                                        '%' . $transaction->no_dokumen . '%',
                                    )
                                        ->where('type', 'out')
                                        ->sum('quantity');

                                    // 3. Cek apakah masih ada sisa yang bisa diretur
                                    $bisaReturLagi = $totalDiterima > $totalSudahRetur;
                                @endphp

                                {{-- Tombol Retur hanya muncul jika status sukses DAN masih ada sisa barang --}}
                                @if ($transaction->status === 'sukses' && $bisaReturLagi)
                                    <button type="button" class="btn btn-warning text-dark" data-bs-toggle="modal"
                                        data-bs-target="#returModal">
                                        <i class="bi bi-arrow-counterclockwise"></i> Retur Barang
                                    </button>
                                @elseif($transaction->status === 'sukses' && !$bisaReturLagi)
                                    <button class="btn btn-danger" disabled>
                                        <i class="bi bi-check-all"></i> Semua Barang Sudah Diretur
                                    </button>
                                @endif



                                {{-- <div class="modal fade" id="returModal" tabindex="-1" aria-labelledby="returModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title fw-bold" id="returModalLabel">
                                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Form Retur Barang
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('v2sparepartinmultiple.retur', $transaction->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Pilih barang dan jumlah yang ingin dikembalikan ke supplier.</p>

                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Sparepart</th>
                                                                <th>Diterima</th>
                                                                <th>Jumlah Retur</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($transaction->stockTransactions as $item)
                                                                <tr>
                                                                    <td>{{ $item->sparePart->name }}</td>
                                                                    <td>{{ (int) $item->quantity }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="sparepart_id[]"
                                                                            value="{{ $item->spare_part_id }}">
                                                                        <input type="number" name="qty_retur[]"
                                                                            class="form-control form-control-sm"
                                                                            max="{{ (int) $item->quantity }}"
                                                                            min="0" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    <div class="form-group mt-3">
                                                        <label class="mb-2">Alasan Retur:</label>
                                                        <textarea name="alasan_retur" class="form-control" rows="3"
                                                            placeholder="Contoh: Barang cacat/tidak sesuai spesifikasi" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-warning text-dark">Proses
                                                        Retur</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> --}}

                                @if (session('success'))
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                    <script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: "{{ session('success') }}",
                                            timer: 3000,
                                            showConfirmButton: false
                                        });
                                    </script>
                                @endif

                                <a href="{{ url()->previous() }}" class="btn btn-secondary"
                                    style="position: relative; z-index: 9999;">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>

                                {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a> --}}

                                {{-- <a href="{{ route('v2sparepartinmultiple.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a> --}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="backorderModal" tabindex="-1" aria-labelledby="backorderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="backorderModalLabel fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Barang Kurang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda menerima barang <strong>lebih sedikit</strong> dari jumlah yang dipesan.</p>
                    <p>Apakah Anda ingin membuat <strong>Backorder</strong> untuk sisa barang yang belum datang?</p>
                    <div class="alert alert-light border small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Backorder:</strong> Sistem akan membuat dokumen baru untuk menagih sisa barang nanti.<br>
                        <strong>Tutup Sisa:</strong> Sisa barang dianggap batal dan surat pesanan akan ditutup.
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" onclick="submitFinal('backorder')" class="btn btn-success">
                        <i class="bi bi-truck me-1"></i>Buat Backorder
                    </button>
                    <button type="button" onclick="submitFinal('close')" class="btn btn-outline-danger">
                        <i class="bi bi-check-all me-1"></i>Tutup Sisa (Batal)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="action_type" id="action_type" form="approveForm" value="backorder">

    <div class="modal fade" id="returModal" tabindex="-1" aria-labelledby="returModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="returModalLabel">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Form Retur Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('v2sparepartinmultiple.retur', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Pilih barang dan jumlah yang ingin dikembalikan. Sisa barang akan
                            terhitung otomatis.</p>

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sparepart</th>
                                    <th>Diterima</th>
                                    <th>Sudah Retur</th>
                                    <th>Sisa Bisa Retur</th>
                                    <th>Jumlah Retur Sekarang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->stockTransactions as $item)
                                    @php
                                        // Hitung retur spesifik untuk item ini saja
                                        $itemTeretur = \App\Models\StockTransactionModel::where(
                                            'spare_part_id',
                                            $item->spare_part_id,
                                        )
                                            ->where('keterangan', 'LIKE', '%' . $transaction->no_dokumen . '%')
                                            ->where('type', 'out')
                                            ->sum('quantity');

                                        $sisaItem = (int) $item->quantity - (int) $itemTeretur;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->sparePart->name }}</td>
                                        <td>{{ (int) $item->quantity }}</td>
                                        <td><span class="text-danger">{{ (int) $itemTeretur }}</span>
                                        </td>
                                        <td><span class="fw-bold">{{ $sisaItem }}</span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="sparepart_id[]"
                                                value="{{ $item->spare_part_id }}">

                                            @if ($sisaItem > 0)
                                                <input type="number" name="qty_retur[]"
                                                    class="form-control form-control-sm" max="{{ $sisaItem }}"
                                                    min="0" value="0">
                                            @else
                                                <span class="badge bg-success text-white">sudah retur semua</span>
                                                <input type="hidden" name="qty_retur[]" value="0">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group mt-3">
                            <label class="mb-2">Alasan Retur:</label>
                            <textarea name="alasan_retur" class="form-control" rows="3"
                                placeholder="Contoh: Barang cacat/tidak sesuai spesifikasi" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-warning text-dark">Proses
                            Retur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi pembersihan
            function clearModalArtifacts() {
                // 1. Hapus backdrop hitam transparan
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(b => b.remove());

                // 2. Kembalikan fungsi klik dan scroll pada body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0';
                document.body.removeAttribute('style');
            }

            // Jalankan pembersihan setiap kali halaman dimuat (setelah redirect)
            clearModalArtifacts();

            // Jalankan pembersihan jika modal ditutup secara manual
            const myModal = document.getElementById('returModal');
            if (myModal) {
                myModal.addEventListener('hidden.bs.modal', function() {
                    clearModalArtifacts();
                });
            }
        });
    </script>

    <script>
        function validateQty(input, max) {
            let val = parseInt(input.value) || 0;
            if (val > max) {
                input.value = max;
                val = max;
            }

            // Update tampilan sisa gantung di bawah input
            let row = input.closest('tr');
            let sisa = max - val;
            row.querySelector('.sisa-gantung').innerText = sisa;
        }
        // function validateQty(input, maxQty) {
        //     let val = parseInt(input.value);
        //     if (val > maxQty) {
        //         alert('Qty Datang tidak boleh melebihi jumlah pesanan (' + maxQty + ')');
        //         input.value = maxQty;
        //     }
        //     if (val < 0 || isNaN(val)) {
        //         input.value = 0;
        //     }
        // }
    </script>

    <script>
        function checkBackorder() {
            let isLess = false;
            const qtyInputs = document.querySelectorAll('.qty-input');
            const demandInputs = document.getElementsByName('demand[]');

            // Cek apakah ada qty_datang yang kurang dari demand
            qtyInputs.forEach((input, index) => {
                let datang = parseInt(input.value) || 0;
                let pesan = parseInt(demandInputs[index].value) || 0;

                if (datang < pesan) {
                    isLess = true;
                }
            });

            if (isLess) {
                // Jika ada yang kurang, munculkan Modal konfirmasi
                var myModal = new bootstrap.Modal(document.getElementById('backorderModal'));
                myModal.show();
            } else {
                // Jika pas semua, langsung submit sebagai 'close'
                submitFinal('close');
            }
        }

        function submitFinal(action) {
            // Set value hidden input untuk dibaca di Controller
            document.getElementById('action_type').value = action;
            // Jalankan submit form
            document.getElementById('approveForm').submit();
        }
    </script>

    <script>
        new Litepicker({
            element: document.getElementById('tanggalMulai'),
            lang: 'id', // Bahasa Indonesia
            format: 'DD MMMM YYYY', // 29 November 2025
            dropdowns: {
                minYear: 2020,
                maxYear: new Date().getFullYear() + 5,
                months: true,
                years: true
            },
            setup: (picker) => {
                picker.on('selected', (date) => {
                    const mysql = date.format('YYYY-MM-DD');
                    document.getElementById('tanggalHidden').value = mysql;
                });
            }
        });
    </script>
@endpush
