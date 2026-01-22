@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 4)
            <div class="pagetitle">
                <a href="{{ route('sparepartitinmultiple.create') }}" class="btn btn-primary" dusk="createsparepartin">Tambah Spare part IT Masuk</a>
            </div><!-- End Page Title -->
        @endif

        <div class="d-flex justify-content-end mb-2">
            <form method="GET" action="{{ route('sparepartinmultiple.index') }}" class="mb-3 d-flex gap-2 align-items-end">
                <div>
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label for="end_date">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('sparepartinmultiple.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Daftar Spare Part IT Masuk</h5>
                            </div>

                            @if (session('success'))
                                <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: '{{ session('success') }}',
                                        timer: 2000, // 2000 ms = 2 detik
                                        showConfirmButton: false
                                    });
                                </script>
                            @endif

                            <!-- Default Table -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No Dokumen</th>
                                            <th class="text-center">Penerima Barang</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Keterangan</th>
                                            @if (Auth::user()->is_role == 2)
                                                <th class="text-center">Aksi</th>
                                            @endif

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $index => $in)
                                            <tr onclick="window.location='{{ route('sparepartitinmultiple.show', $in->id) }}'"
                                                style="cursor:pointer;">
                                                <td class="text-center">{{ $in->no_dokumen }}</td>
                                                <td class="text-center">{{ $in->diterima_oleh ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($in->tanggal)->format('d-m-Y') }}</td>
                                                {{-- <td class="text-center"><span class="badge bg-success">Success</span></td> --}}
                                                <td class="text-center">
                                                    @if ($in->status === 'sukses')
                                                        <span class="badge bg-success">sukses</span>
                                                    @elseif ($in->status === 'batal')
                                                        <span class="badge bg-danger">Batal</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="text-center">{{ $in->keterangan ?? '-' }}</td>

                                                <td class="text-center" onclick="event.stopPropagation();">
                                                    @if (Auth::user()->is_role == 2 && $in->status === 'sukses')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#batalModal{{ $in->id }}">
                                                            Batal
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="batalModal{{ $in->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('atkmasuk.batal', $in->id) }}" method="POST">
                                                        @csrf

                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger">
                                                                    Konfirmasi Pembatalan
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <p>
                                                                    Yakin ingin membatalkan transaksi
                                                                    <strong>{{ $in->no_dokumen }}</strong>?
                                                                </p>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Alasan Pembatalan</label>
                                                                    <textarea name="keterangan" class="form-control" rows="3" required placeholder="Wajib diisi..."></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    Konfirmasi Batal
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center">
                                {{ $transactions->links() }}
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
