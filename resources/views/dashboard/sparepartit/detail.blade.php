@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        {{-- ================= TITLE ================= --}}
        <div class="pagetitle mb-3">
            <h1>Riwayat Spare Part IT : {{ $sparePart->name }}</h1>
        </div>

        {{-- ================= FILTER ================= --}}
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
                    <a href="{{ route('sparepartdetail.history', $sparePart->id) }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <div>
                    <a href="{{ route('sparepartdetail.history.pdf', $sparePart->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                        class="btn btn-success">Print PDF</a>
                </div>
                <div>
                    <a href="{{ route('sparepartdetail.history.excel', [
                        'id' => $sparePart->id,
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                    ]) }}"
                        class="btn btn-success" target="_blank">
                        Export XLS
                    </a>
                </div>
            @endif
        </div>

        {{-- ================= TABLE ================= --}}
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- ALERT --}}
                            @if (session('success'))
                                <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: '{{ session('success') }}',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                </script>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Lokasi</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Tipe</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Keterangan</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($transactions as $index => $item)
                                            @php
                                                $total = $item->quantity * $item->price;
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    {{ $transactions->firstItem() + $index }}
                                                </td>

                                                <td class="text-center">{{ $item->user ?? '-' }}</td>

                                                {{-- JUMLAH --}}
                                                <td class="text-center">
                                                    <span
                                                        class="{{ $item->status === 'batal' ? 'text-danger fw-bold' : '' }}">
                                                        {{ $item->quantity }}
                                                    </span>
                                                </td>

                                                {{-- LOKASI --}}
                                                <td class="text-center">
                                                    {{ $item->atkKeluar->locations->name ?? '-' }}
                                                </td>

                                                {{-- TANGGAL --}}
                                                <td class="text-center">
                                                    {{ $item->created_at->format('d-m-Y') }}
                                                </td>

                                                {{-- TIPE --}}
                                                <td class="text-center">
                                                    {{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}
                                                </td>
                                                {{-- <td class="text-center">
                                                    <span
                                                        class="badge {{ $item->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $item->type === 'in' ? 'Masuk' : 'Keluar' }}
                                                    </span>
                                                </td> --}}

                                                {{-- STATUS --}}
                                                <td class="text-center">
                                                    <span
                                                        class="badge {{ $item->status === 'batal' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>

                                                {{-- KETERANGAN --}}
                                                <td class="text-center">
                                                    {{ $item->keterangan ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- FOOTER --}}
                                        @if ($transactions->currentPage() === $transactions->lastPage())
                                            <tr class="table-light">
                                                <td colspan="2" class="text-end">
                                                    <strong>Jumlah Akhir Stok</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ $lastRunningStock }}</strong>
                                                </td>

                                                <td colspan="4"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- PAGINATION --}}
                            <div class="d-flex justify-content-center">
                                {{ $transactions->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
