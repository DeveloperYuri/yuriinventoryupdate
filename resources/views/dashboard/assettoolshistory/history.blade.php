@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle mb-5">
            <h1 class="text-center">History Asset Tools In / Out</h1>
        </div>

        <div class="d-flex justify-content-end align-items-end gap-3 flex-wrap mb-3">

            <form method="GET" class="d-flex align-items-end gap-2 flex-wrap">
                <div class="col">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="col">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>
                <div class="col d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('assettools.history') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <div>
                    <a href="{{ route('assettools.history.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn btn-success">Print PDF</a>
                </div>
                <div>
                    <a href="{{ route('assettools.history.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn btn-success" target="_blank">
                        <i class="fas fa-file-excel"></i> Export XLS
                    </a>
                </div>
            @endif

        </div>

        {{-- <div class="d-flex justify-content-end align-items-end mb-3 gap-3 flex-wrap">

            <form method="GET" action="{{ route('assettools.history') }}" class="d-flex gap-2">
                <div>
                    <label for="start_date" class="form-label">Dari:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date') }}">
                </div>
                <div>
                    <label for="end_date" class="form-label">Sampai:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date') }}">
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('assettools.history') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <div class="d-flex align-items-end">
                    <a href="{{ route('assettools.history.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-success">Cetak PDF</a>
                
                    <a href="{{ route('assettools.history.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                        class="btn btn-success ms-2" target="_blank">
                        <i class="fas fa-file-excel"></i> Export XLS
                     </a>
                
                </div>
            @endif
        </div> --}}


        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">



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
                                        <th class="text-center">Nama Spare Part</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Keterangan</th>
                                    </tr>
                                    @foreach ($transactions as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                                            <td class="text-center">{{ $item->assetTools->name ?? '-' }}</td>
                                            <td class="text-center">{{ $item->user }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">{{ $item->created_at->format('d-m-Y') }}</td>
                                            <td class="text-center">
                                                @if ($item->type == 'in')
                                                    <span class="badge bg-success">Masuk</span>
                                                @else
                                                    <span class="badge bg-danger">Keluar</span>
                                                @endif
                                            </td>

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
