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
                                <div class="col-md-8">
                                    <h2 class="mt-2">Form Detail Pesanan Barang ATK</h2>
                                </div>

                                <!-- Kanan: Tombol Print PDF -->
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('suratpesanan-atk.pdf', $transaction->id) }}" target="_blank"
                                        class="btn btn-primary mt-2">
                                        Print PDF
                                    </a>
                                </div>
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
                                                    value="{{ $transaction->dibuat_oleh }}" readonly>
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
                                    </div>
                                </div>

                                <!-- Tab -->
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama ATK</th>
                                                    <th>Qty</th>
                                                    <th>Stock</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction->details as $item)
                                                    <tr>
                                                        <td>{{ $item->atk->name ?? '-' }}</td>
                                                        <td>{{ $item->qty }}</td>
                                                        <td>{{ $item->stock }}</td>
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

                            <a href="{{ route('suratpesanan-atk.index') }}" class="btn btn-success mt-2">Back</a>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
