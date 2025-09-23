@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
            <div class="pagetitle">
                <a href="{{ route('asset-in.create') }}" class="btn btn-primary">Create New Asset Tools In</a>
            </div>
        @endif

        <div class="d-flex justify-content-end mb-2">
            <form method="GET" action="{{ route('asset-in.index') }}" class="mb-3 d-flex gap-2 align-items-end">
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
                    <a href="{{ route('asset-in.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>


        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h5 class="card-title">List Asset Tools In</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('export.asset-stock-in') }}" class="btn btn-success"
                                            target="_blank">Print
                                            PDF</a>
                                        <a href="{{ route('assetstockin.export.excel') }}" class="btn btn-success">Export
                                            XLX</a>
                                    </div>
                                @endif

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
                                            <th class="text-center">No</th>
                                            <th class="text-center">Name Asset</th>
                                            <th class="text-center">Penerima</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($transactions as $index => $assetin)
                                            <tr>
                                                <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                                                <td class="text-center">{{ $assetin->assetTools->name }}</td>
                                                <td class="text-center">{{ $assetin->user }}</td>
                                                <td class="text-center">{{ $assetin->quantity }}</td>
                                                <td class="text-center">{{ $assetin->created_at->format('d-m-Y') }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <!-- End Default Table Example -->
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
