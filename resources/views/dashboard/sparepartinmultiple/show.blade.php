@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-2">Detail Penerimaan Barang</h2>

                            <form class="mt-4" action="{{ route('sparepartinmultiple.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <!-- Kiri -->
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
                                                <input type="text" class="form-control" name="diterima_dari"
                                                    value="{{ $transaction->diterima_dari }}" readonly>
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
                                            <label class="col-sm-4 col-form-label">Di terima oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="diterima_oleh"
                                                    value="{{ $transaction->diterima_oleh }}" readonly>
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
                                                    <th>Spare Part</th>
                                                    <th>Qty</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction->stockTransactions as $item)
                                                    <tr>
                                                        <td>{{ $item->sparePart->name ?? '-' }}</td>
                                                        <td>{{ $item->quantity }}</td>
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

                            <a href="{{ route('sparepartinmultiple.index') }}" class="btn btn-success mt-2">Back</a>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
