@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
            <div class="pagetitle">
                <a href="{{ route('stock-in.create') }}" class="btn btn-primary">Create New Spare Part In</a>
            </div><!-- End Page Title -->
        @endif

        <div class="d-flex justify-content-end mb-2">
            <form method="GET" action="{{ route('stock-in.index') }}" class="mb-3 d-flex gap-2 align-items-end">
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
                    <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h5 class="card-title">List Spare Part In</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('export.stock-in') }}" class="btn btn-success">Print PDF</a>
                                        <a href="{{ route('stockin.export.excel') }}" class="btn btn-success">Export XLX</a>
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
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Spare Part</th>
                                        <th class="text-center">Penerima</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Tanggal</th>
                                    </tr>
                                    @foreach ($transactions as $index => $in)
                                        <tr>
                                            <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                                            <td class="text-center">{{ $in->sparePart->name }}</td>
                                            <td class="text-center">{{ $in->user }}</td>
                                            <td class="text-center">{{ $in->quantity }}</td>
                                            <td class="text-center">{{ $in->created_at->format('d-m-Y') }}</td>
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
