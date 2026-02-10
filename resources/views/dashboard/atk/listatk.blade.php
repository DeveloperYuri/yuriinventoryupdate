@extends('dashboard.layouts.main')

@section('content')
    <style>
        /* Sembunyikan kolom secara default saat halaman dimuat (Refresh) */
        /* Kolom 6: Stok Awal, 7: Masuk, 8: Keluar, dan Kolom Status (sesuaikan index jika ada) */
        .table th:nth-child(4),
        .table td:nth-child(4),
        .table th:nth-child(5),
        .table td:nth-child(5),
        .table th:nth-child(6),
        .table td:nth-child(6),
        .table th:nth-child(9),
        .table td:nth-child(9) {
            display: none;
        }

        /* .table tbody tr {
                        height: 400px;
                        /* Ubah angka ini sesuai keinginan */
        }

        */
    </style>

    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 3 || Auth::user()->is_role == 2)
                <a href="{{ route('atk.create') }}" class="btn btn-primary">Tambah ATK</a>
            @endif

            <a href="{{ route('cardlist-atk.index') }}" class="btn btn-secondary"><i class="bi bi-card-list"></i></a>
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <small class="text-muted">Nama ATK</small>
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->name }}"
                            placeholder="Cari Nama ATK" name="name">
                    </div>

                    <div class="col-3">
                        <small class="text-muted">Category</small>
                        <select id="category_id" name="category_id"
                            class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">-- Pilih Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-2">
                        <small class="text-muted">Status</small>
                        <select name="status_atk_id" class="form-control">
                            <option value="">-- Semua Status --</option>
                            @foreach ($produkstatus as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('status_atk_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-2">
                        <small class="text-muted">Pilih Periode</small>
                        <input type="month" name="period" class="form-control" value="{{ request('period') }}">
                    </div>

                    <div class="col-auto">
                        <small class="d-block">&nbsp;</small>
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('atk.index') }}" class="btn btn-secondary">Reset</a>

                        {{-- <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">Reset</a> --}}

                    </div>
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row mt-4">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Daftar ATK</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 3)
                                    <div class="d-flex gap-2">
                                        <!-- Button Import -->
                                        {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#importModal">
                                            Import Excel
                                        </button> --}}

                                        <!-- Modal Import -->
                                        {{-- <div class="modal fade" id="importModal" tabindex="-1"
                                            aria-labelledby="importModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importModalLabel">Import Spare Part dari
                                                            Excel</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('spare-parts.import') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="file" class="form-label">Pilih File
                                                                    Excel</label>
                                                                <input type="file" name="file" class="form-control"
                                                                    accept=".xlsx,.xls,.csv" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary"
                                                                id="btnImport">Import</button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <a href="{{ route('atk.cetakpdf') }}" class="btn btn-success" target="_blank">Print
                                            PDF</a>
                                        <a href="{{ route('atk.export') }}" class="btn btn-success">Export Excel</a>

                                        <a href="{{ route('atk.exportmultiple', ['period' => $period]) }}"
                                            class="btn btn-success">
                                            Export Excel by Category
                                        </a>
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
                                            {{-- <th class="text-center">No</th> --}}
                                            <th class="text-center">Gambar</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Kategori</th>
                                            {{-- @if (Auth::user()->is_role == 2)
                                                <th class="text-center">Harga</th>
                                            @endif --}}
                                            <th class="text-center">Stok Awal</th>
                                            <th class="text-center">Masuk</th>
                                            <th class="text-center">Keluar</th>
                                            <th class="text-center">Stok Akhir</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Status</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 3)
                                                <th class="text-center">Aksi</th>
                                            @endif

                                            <th class="text-center">
                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" id="filterColumn" data-bs-toggle="dropdown"
                                                        data-bs-auto-close="outside" aria-expanded="false"
                                                        class="text-dark">
                                                        <i class="bi bi-filter"></i>
                                                    </a>
                                                    <ul class="dropdown-menu shadow-sm p-3" aria-labelledby="filterColumn"
                                                        style="min-width: 200px; font-size: 14px;">
                                                        <li>
                                                            <h6 class="dropdown-header px-0 text-dark">Tampilkan Kolom</h6>
                                                        </li>

                                                        @php
                                                            $columns = [
                                                                ['id' => 4, 'name' => 'Stok Awal'],
                                                                ['id' => 5, 'name' => 'Masuk'],
                                                                ['id' => 6, 'name' => 'Keluar'],
                                                                ['id' => 9, 'name' => 'Status'],
                                                            ];
                                                        @endphp

                                                        @foreach ($columns as $col)
                                                            <li>
                                                                <div class="form-check mb-1">
                                                                    <input class="form-check-input toggle-vis"
                                                                        type="checkbox" value="{{ $col['id'] }}"
                                                                        id="check{{ $col['id'] }}">
                                                                    {{-- Tanpa atribut 'checked' --}}
                                                                    <label class="form-check-label"
                                                                        for="check{{ $col['id'] }}">
                                                                        {{ $col['name'] }}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($getRecord as $index => $atk)
                                            <tr>
                                                {{-- <td class="text-center">{{ $getRecord->firstItem() + $index }}</td> --}}
                                                <td class="text-center">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#imageModal{{ $atk->id }}">
                                                        <img src="{{ asset('images/' . ($atk->image ?? 'default.png')) }}"
                                                            class="img-thumbnail"
                                                            style="width: 100px; height: 70px; object-fit: contain;">
                                                    </a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="imageModal{{ $atk->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content bg-transparent border-0 shadow-none">
                                                                <div class="modal-body text-center p-0">
                                                                    <img src="{{ asset('images/' . ($atk->image ?? 'default.png')) }}"
                                                                        class="img-fluid rounded"
                                                                        style="max-height: 90vh;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- <td class="text-center">
                                                    @if ($atk->image)
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#imageModal{{ $atk->id }}">
                                                            <img src="{{ asset('images/' . $atk->image) }}"
                                                                class="img-thumbnail"
                                                                style="width: 100px; height: 70px; object-fit: contain;">
                                                        </a>

                                                        <!-- Modal polos -->
                                                        <div class="modal fade" id="imageModal{{ $atk->id }}"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                <div
                                                                    class="modal-content bg-transparent border-0 shadow-none">
                                                                    <div class="modal-body text-center p-0">
                                                                        <img src="{{ asset('images/' . $atk->image) }}"
                                                                            class="img-fluid rounded"
                                                                            style="max-height: 90vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td> --}}


                                                <td class="text-center">{{ $atk->name }}</td>
                                                <td class="text-center">{{ $atk->category->name ?? '-' }}</td>
                                                {{-- @if (Auth::user()->is_role == 2)
                                                    <td class="text-center">Rp
                                                        {{ number_format($atk->price, 0, ',', '.') }}
                                                    </td>
                                                @endif --}}
                                                {{-- <td class="text-center">{{ $atk->getTotalIn() }}</td>
                                                <td class="text-center">{{ $atk->getTotalOut() }}</td>
                                                <td class="text-center">{{ $atk->stock }}</td>
                                                <td class="text-center">{{ $atk->stock }}</td> --}}
                                                <td class="text-center">{{ $atk->stock_awal }}</td>
                                                <td class="text-center">{{ $atk->masuk }}</td>
                                                <td class="text-center">{{ $atk->keluar }}</td>
                                                <td class="text-center">{{ $atk->stock_akhir }}</td>
                                                <td class="text-center">{{ $atk->satuan->name ?? '-' }}</td>
                                                <td class="text-center">
                                                    @if ($atk->produkstatus)
                                                        <span
                                                            class="badge {{ strtolower($atk->produkstatus->name) == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $atk->produkstatus->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (Auth::user()->is_role == 3 || Auth::user()->is_role == 2)
                                                        <a href="{{ route('atk.edit', $atk->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">Edit</a>

                                                        <a href="{{ route('atk.detail', ['id' => $atk->id]) }}"
                                                            class="btn btn-info btn-sm mt-1">
                                                            History Detail
                                                        </a>
                                                    @endif

                                                    @if (Auth::user()->is_role == 2)
                                                        <form action="{{ route('atk.destroy', $atk->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger mt-1"
                                                                onclick="confirmDelete(this.form)">Hapus</button>
                                                        </form>
                                                    @endif
                                                </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- End Default Table Example -->
                            </div>

                            @push('scripts')
                                <script>
                                    function confirmDelete(form) {
                                        Swal.fire({
                                            title: 'Yakin ingin hapus?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#d33',
                                            cancelButtonColor: '#3085d6',
                                            confirmButtonText: 'Ya, hapus!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                form.submit();
                                            }
                                        });
                                    }
                                </script>
                            @endpush

                            <!-- PAGINATION LINK -->
                            <div class="d-flex justify-content-center">
                                {{ $getRecord->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><!-- End #main -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const importForm = document.querySelector('#importModal form');
            const btnImport = document.getElementById('btnImport');

            importForm.addEventListener('submit', function() {
                btnImport.disabled = true;
                btnImport.innerHTML = 'Importing...';
            });
        });
    </script>

    <script>
        $(function() {
            $("#searchingtitle").autocomplete({
                source: "{{ route('atk.autocomplete') }}",
                minLength: 2, // mulai search setelah 2 karakter
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.toggle-vis').on('change', function() {
                // Kita gunakan value langsung sebagai urutan kolom (nth-child)
                var colIndex = $(this).val();

                var cells = $('table.table').find('th:nth-child(' + colIndex + '), td:nth-child(' +
                    colIndex + ')');

                if ($(this).is(':checked')) {
                    cells.show();
                } else {
                    cells.hide();
                }
            });
        });
    </script>
@endpush

@if ($errors->any())
    @push('scripts')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal import!',
                text: '{{ $errors->first() }}'
            });
        </script>
    @endpush
@endif
