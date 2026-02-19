@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="row mb-4 align-items-center">
                                <!-- Kiri: Judul / Form Header -->
                                <div class="col-md-6">
                                    <h2 class="mt-2">Form Detail Pesanan Barang</h2>
                                </div>

                                <div class="text-end">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <a href="{{ route('suratpesanan.pdf', $transaction->id) }}" target="_blank"
                                            class="btn btn-primary btn-sm me-2">
                                            <i class="bi bi-printer me-1"></i> Print PDF
                                        </a>

                                        <div class="d-flex align-items-center me-3">
                                            @php
                                                $statusPenerimaan = $transaction->status_penerimaan ?? 'pending';
                                                $dotColor =
                                                    [
                                                        'open' => '#6c757d', // Abu-abu
                                                        'proses' => '#0dcaf0', // Biru muda
                                                        'closed' => '#198754', // Hijau
                                                        'batal' => '#dc3545', // Merah
                                                    ][$statusPenerimaan] ?? '#6c757d';
                                            @endphp

                                            <span
                                                style="height: 12px; width: 12px; background-color: {{ $dotColor }}; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>

                                            <span style="font-weight: 600; color: {{ $dotColor }}; font-size: 0.9rem;">
                                                {{ ucfirst($statusPenerimaan) }}
                                            </span>
                                        </div>

                                        {{-- <div class="me-2">
                                            @php
                                                $statusPenerimaan = $transaction->status_penerimaan ?? 'pending';
                                                $badgeColor =
                                                    [
                                                        'pending' => 'bg-secondary',
                                                        'proses' => 'bg-info',
                                                        'sukses' => 'bg-success',
                                                        'batal' => 'bg-danger',
                                                    ][$statusPenerimaan] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $badgeColor }} p-2" style="font-size: 0.9rem;">
                                                <i class="bi bi-truck me-1"></i>
                                                {{ ucfirst($statusPenerimaan) }}
                                            </span>
                                        </div> --}}
                                    </div>
                                </div>
                                <!-- Kanan: Tombol Print PDF -->
                                {{-- <div class="col-md-6 text-end">
                                    <a href="{{ route('suratpesanan.pdf', $transaction->id) }}" target="_blank"
                                        class="btn btn-primary mt-2">
                                        Print PDF
                                    </a>
                                </div> --}}
                            </div>

                            <form class="mt-4" action="{{ route('sparepartinmultiple.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <!-- Kiri -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No Dokumen</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="no_dokumen"
                                                    value="{{ $transaction->no_surat_pesanan }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di buat oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ $transaction->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Category</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ $transaction->category->name ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Ditujukan kepada</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" disabled>
                                                    <option value="JF"
                                                        {{ $transaction->ditujukan_kepada == 'JF' ? 'selected' : '' }}>Ko Jefri
                                                    </option>
                                                    <option value="WD"
                                                        {{ $transaction->ditujukan_kepada == 'WD' ? 'selected' : '' }}>Bu Widy
                                                    </option>
                                                    <option value="NR"
                                                        {{ $transaction->ditujukan_kepada == 'NR' ? 'selected' : '' }}>Bu Nur
                                                    </option>
                                                    <option value="SA"
                                                        {{ $transaction->ditujukan_kepada == 'SA' ? 'selected' : '' }}>Sumber Alam
                                                    </option>
                                                    <option value="LN"
                                                        {{ $transaction->ditujukan_kepada == 'LN' ? 'selected' : '' }}>Lainnya
                                                    </option>
                                                </select>
                                                @error('ditujukan_kepada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Kanan -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Date</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d-m-Y') }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Lokasi</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ $transaction->location->name ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Sub Category</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ $transaction->subcategory->name ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab -->
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama Spare Part</th>
                                                    <th class="text-center">Qty Minta</th>
                                                    <th class="text-center">Stock</th>
                                                    <th class="text-center">Qty Yang Harus Dibeli</th>
                                                    <th class="text-center">Qty Datang</th> {{-- Tambahan Kolom Baru --}}
                                                    <th class="text-center">Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction->details as $item)
                                                    <tr>
                                                        <td>{{ $item->sparePart->name ?? '-' }}</td>
                                                        <td class="text-center">{{ $item->qty }}</td>
                                                        <td class="text-center">{{ $item->stock }}</td>
                                                        <td class="text-center">{{ $item->qty_kurang }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                // Ambil total quantity yang sudah masuk berdasarkan No Pesanan ini dan ID Part ini
                                                                // Diasumsikan di tabel stock_transactions ada kolom 'referensi' atau relasi ke header yang punya referensi
                                                                $totalDatang = \App\Models\StockTransactionModel::whereHas(
                                                                    'header',
                                                                    function ($q) use ($transaction) {
                                                                        $q->where(
                                                                            'referensi',
                                                                            $transaction->no_surat_pesanan,
                                                                        );
                                                                    },
                                                                )
                                                                    ->where('spare_part_id', $item->spare_part_id)
                                                                    ->where('status', 'sukses')
                                                                    ->sum('quantity');
                                                            @endphp

                                                            <span class="text-center">
                                                                {{ $totalDatang }}
                                                            </span>
                                                        </td class="text-center">
                                                        <td>{{ $item->keterangan }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="additional" role="tabpanel">
                                        <textarea class="form-control" rows="4" placeholder="Additional Information"></textarea>
                                    </div>
                                    <div class="tab-pane fade" id="note" role="tabpanel">
                                        <textarea class="form-control" rows="4" placeholder="Note"></textarea>
                                    </div>
                                </div>
                            </form>

                            <a href="{{ route('v2suratpesanan.index') }}" class="btn btn-success mt-2">Back</a>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
