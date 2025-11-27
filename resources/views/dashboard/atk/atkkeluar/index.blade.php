@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 3)
            <div class="pagetitle">
                <a href="{{ route('atk-keluar.create') }}" class="btn btn-primary" dusk="createsparepartin">Tambah ATK Keluar</a>
            </div><!-- End Page Title -->
        @endif

        <div class="d-flex justify-content-end mb-2">
            <form method="GET" action="{{ route('sparepartoutmultiple.index') }}" class="mb-3 d-flex gap-2 align-items-end">
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
                    <a href="{{ route('sparepartoutmultiple.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Daftar ATK Keluar</h5>
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
                                            <th class="text-center">Di minta oleh</th>
                                            <th class="text-center">Lokasi</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $index => $out)
                                            <tr onclick="window.location='{{ route('atk-keluar.show', $out->id) }}'"
                                                style="cursor:pointer;">
                                                <td class="text-center">{{ $out->no_dokumen }}</td>
                                                <td class="text-center">{{ $out->diminta_oleh ?? '-' }}</td>
                                                <td class="text-center">{{ $out->locations->name ?? '-' }}</td>

                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($out->tanggal)->format('d-m-Y') }}</td>
                                                <td class="text-center"><span class="badge bg-success">Success</span></td>
                                            </tr>
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
