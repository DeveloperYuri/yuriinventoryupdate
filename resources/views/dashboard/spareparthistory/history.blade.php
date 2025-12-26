@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle mb-5">
            <h1 class="text-center">Riwayat Spare Part Masuk / Keluar</h1>
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
                    <a href="{{ route('sparepart.history') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <div>
                    <a href="{{ route('sparepart.history.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn btn-success">Print PDF</a>
                </div>
                <div>
                    <a href="{{ route('sparepart.history.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn btn-success" target="_blank">
                        Export XLX
                    </a>
                </div>
            @endif
        </div>

        {{-- <div class="d-flex justify-content-end align-items-end mb-3 gap-3 flex-wrap">

            <form method="GET" action="{{ route('sparepart.history') }}" class="d-flex gap-2">
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
                    <a href="{{ route('sparepart.history') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <div class="d-flex align-items-end">
                    <a href="{{ route('sparepart.history.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-success">Print PDF</a>
                    
                    <a href="{{ route('sparepart.history.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
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
                                        <th class="text-center">Lokasi</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Sub Category</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Keterangan</th>
                                        @if (Auth::user()->is_role == 2)
                                            <th class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                    @foreach ($transactions as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                                            <td class="text-center">{{ $item->sparePart->name ?? '-' }}</td>
                                            <td class="text-center">{{ $item->user }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">{{ $item->stockOutHeader->location->name ?? '-' }}</td>
                                            <td class="text-center">{{ $item->stockOutHeader->category->name ?? '-' }}</td>
                                            <td class="text-center">{{ $item->stockOutHeader->subcategory->name ?? '-' }}
                                            </td>
                                            <td class="text-center">{{ $item->created_at->format('d-m-Y') }}</td>
                                            <td class="text-center">
                                                {{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}
                                            </td>

                                            <td class="text-center">
                                                <span
                                                    class="badge 
        @if ($item->status === 'sukses') bg-success
        @elseif ($item->status === 'batal') bg-danger
        @else bg-secondary @endif
    ">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>

                                            {{-- <td class="text-center">{{ $item->status }}</td> --}}
                                            <td class="text-center">{{ $item->keterangan ?? '-' }}</td>

                                            <td class="text-center" onclick="event.stopPropagation();">
                                                @if (Auth::user()->is_role == 2 && $item->status === 'sukses')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#batalModal{{ $item->id }}">
                                                        Batal
                                                    </button>
                                                @endif
                                            </td>

                                        </tr>

                                        <!-- Modal Batal -->
                                        <div class="modal fade" id="batalModal{{ $item->id }}" tabindex="-1"
                                            aria-labelledby="batalModalLabel{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="batalModalLabel{{ $item->id }}">
                                                            Konfirmasi Pembatalan
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form action="{{ route('sparepart.history.batal', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="modal-body">
                                                            <p>
                                                                Yakin ingin membatalkan transaksi Spare Part
                                                                <strong>{{ $item->sparePart->name ?? '-' }}</strong>
                                                                sebanyak <strong>{{ $item->quantity }}</strong>?
                                                            </p>

                                                            <div class="mb-3">
                                                                <label class="form-label">Alasan Pembatalan</label>
                                                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan alasan pembatalan..." required></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">
                                                                Tutup
                                                            </button>
                                                            <button type="submit" class="btn btn-danger">
                                                                Ya, Batalkan
                                                            </button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
