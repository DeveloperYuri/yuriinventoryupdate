@extends('dashboard.layouts.main')

@section('content')
    <style>
        /* Sembunyikan kolom secara default saat halaman dimuat (Refresh) */
        /* Kolom 6: Stok Awal, 7: Masuk, 8: Keluar, dan Kolom Status (sesuaikan index jika ada) */
        .table th:nth-child(6),
        .table td:nth-child(6),
        .table th:nth-child(7),
        .table td:nth-child(7),
        .table th:nth-child(8),
        .table td:nth-child(8),
        .table th:nth-child(11),
        .table td:nth-child(11) {
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
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1 || Auth::user()->is_role == 0)
                <a href="{{ route('spare-parts.create') }}" class="btn btn-primary">Tambah Spare Part</a>
            @endif

            <a href="{{ route('card-list-spare-parts.index') }}" class="btn btn-secondary"><i class="bi bi-card-list"></i></a>
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col-4">
                        <small class="text-muted">Nama Spare Part</small>
                        <input type="text" id="searchingtitle" name="name" class="form-control"
                            value="{{ request('name') }}">


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
                    <div class="col-3">
                        <small class="text-muted">Sub Category</small>
                        <select id="subcategory_id" name="subcategory_id"
                            class="form-control @error('subcategory_id') is-invalid @enderror">
                            <option value="">-- Pilih Sub Category --</option>
                            {{-- jika ada subcategories awal (misal edit), bisa looping di sini --}}

                        </select>
                    </div>

                    <div class="col-2">
                        <small class="text-muted">Status</small>
                        <select name="produk_status_id" class="form-control">
                            <option value="">-- Semua Status --</option>
                            @foreach ($produkstatus as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('produk_status_id') == $status->id ? 'selected' : '' }}>
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
                        <a href="{{ route('spare-parts.index') }}" id="btnReset" class="btn btn-secondary">Reset</a>
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
                                <h5 class="card-title mb-0">Daftar Spare Part</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">
                                        <!-- Button Import -->
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#importModal">
                                            Import Excel
                                        </button>

                                        <!-- Modal Import -->
                                        <div class="modal fade" id="importModal" tabindex="-1"
                                            aria-labelledby="importModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importModalLabel">Import Spare Part
                                                            dari
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
                                        </div>

                                        {{-- <a href="{{ route('sparepart.cetakpdf') }}" class="btn btn-success"
                                            target="_blank">Print PDF</a> --}}
                                        <a href="{{ route('sparepart.cetakpdf', ['period' => request('period')]) }}"
                                            class="btn btn-success">
                                            Cetak PDF
                                        </a>

                                        <a href="{{ route('sparepart.export', ['period' => $period]) }}"
                                            class="btn btn-success">
                                            Export Excel
                                        </a>

                                        <a href="{{ route('sparepart.exportmultiple', ['period' => $period]) }}"
                                            class="btn btn-success">
                                            Export Excel by Category
                                        </a>
                                        {{-- <a href="{{ route('sparepart.export') }}" class="btn btn-success">Export Excel</a> --}}
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
                            <div class="table-responsive" style="min-height: 300px;">

                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            {{-- <th class="text-center">No</th> --}}
                                            <th class="text-center">Gambar</th>
                                            <th class="text-center">Serial Number</th>
                                            <th class="text-center">Nama</th>
                                            {{-- @if (Auth::user()->is_role == 2)
                                                <th class="text-center">Harga</th>
                                            @endif --}}

                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Sub Kategori</th>
                                            <th class="text-center">Stok Awal</th>
                                            <th class="text-center">Masuk</th>
                                            <th class="text-center">Keluar</th>
                                            <th class="text-center">Stok Akhir</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Status</th>


                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1 || Auth::user()->is_role == 0)
                                                <th class="text-center">Aksi</th>
                                            @endif

                                            <th class="text-center">
                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" id="filterColumn"
                                                        data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                        aria-expanded="false" class="text-dark">
                                                        <i class="bi bi-filter"></i>
                                                    </a>
                                                    <ul class="dropdown-menu shadow-sm p-3" aria-labelledby="filterColumn"
                                                        style="min-width: 200px; font-size: 14px;">
                                                        <li>
                                                            <h6 class="dropdown-header px-0 text-dark">Tampilkan Kolom</h6>
                                                        </li>

                                                        @php
                                                            $columns = [
                                                                ['id' => 5, 'name' => 'Stok Awal'],
                                                                ['id' => 6, 'name' => 'Masuk'],
                                                                ['id' => 7, 'name' => 'Keluar'],
                                                                ['id' => 10, 'name' => 'Status'],
                                                            ];
                                                        @endphp

                                                        @foreach ($columns as $col)
                                                            <li>
                                                                <div class="form-check mb-1">
                                                                    <input class="form-check-input toggle-vis"
                                                                        type="checkbox" value="{{ $col['id'] }}"
                                                                        id="check{{ $col['id'] }}"
                                                                        data-column="{{ $col['id'] }}">
                                                                    {{-- Tambahkan ini --}}
                                                                    <label class="form-check-label"
                                                                        for="check{{ $col['id'] }}">
                                                                        {{ $col['name'] }}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach

                                                        {{-- @foreach ($columns as $col)
                                                            <li>
                                                                <div class="form-check mb-1">
                                                                    <input class="form-check-input toggle-vis"
                                                                        type="checkbox" value="{{ $col['id'] }}"
                                                                        id="check{{ $col['id'] }}">
                                                                   
                                                                    <label class="form-check-label"
                                                                        for="check{{ $col['id'] }}">
                                                                        {{ $col['name'] }}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach --}}
                                                    </ul>
                                                </div>
                                            </th>

                                            {{-- <th class="text-center"><i class="bi bi-filter"></i></th> --}}

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($getRecord as $index => $part)
                                            <tr>
                                                <td class="text-center">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#imageModal{{ $part->id }}">
                                                        <img src="{{ asset('images/' . ($part->image ?? 'default.png')) }}"
                                                            class="img-thumbnail"
                                                            style="width: 100px; height: 70px; object-fit: contain;">
                                                    </a>

                                                    <div class="modal fade" id="imageModal{{ $part->id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content bg-transparent border-0 shadow-none">
                                                                <div class="modal-body text-center p-0">
                                                                    <img src="{{ asset('images/' . ($part->image ?? 'default.png')) }}"
                                                                        class="img-fluid rounded"
                                                                        style="max-height: 90vh;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                </td>
                                                <td class="text-center">
                                                    {{ !empty($part->numbers) ? $part->numbers : '000-000-000' }}
                                                </td>
                                                <td class="text-center">{{ $part->name }}</td>
                                                {{-- @if (Auth::user()->is_role == 2)
                                                    <td class="text-center">Rp
                                                        {{ number_format($part->price, 0, ',', '.') }}
                                                    </td>
                                                @endif --}}

                                                <td class="text-center">{{ $part->category->name ?? '-' }}</td>
                                                <td class="text-center">{{ $part->subcategory->name ?? '-' }}</td>
                                                <td class="text-center">{{ $part->stock_awal }}</td>
                                                <td class="text-center">{{ $part->masuk }}</td>
                                                <td class="text-center">{{ $part->keluar }}</td>
                                                <td class="text-center">{{ $part->stock_akhir }}</td>
                                                {{-- <td class="text-center">{{ $part->getTotalIn() }}</td>
                                                <td class="text-center">{{ $part->getTotalOut() }}</td> --}}
                                                {{-- <td class="text-center">{{ $part->stock }}</td> --}}
                                                <td class="text-center">
                                                    {{ $part->satuan->name ?? '-' }}
                                                </td>

                                                <td class="text-center">
                                                    @if ($part->produkstatus)
                                                        <span
                                                            class="badge {{ strtolower($part->produkstatus->name) == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $part->produkstatus->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>

                                                {{-- <td class="text-center">{{ $part->satuan }}</td> --}}


                                                <td class="text-center">
                                                    @if (Auth::user()->is_role == 1 || Auth::user()->is_role == 2 || Auth::user()->is_role == 0)
                                                        <a href="{{ route('spare-parts.edit', $part->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">Edit</a>

                                                        <a href="{{ route('sparepartdetail.history', ['id' => $part->id]) }}"
                                                            class="btn btn-info btn-sm mt-1">
                                                            History Detail
                                                        </a>
                                                    @endif

                                                    @if (Auth::user()->is_role == 2)
                                                        <form action="{{ route('spare-parts.destroy', $part->id) }}"
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
        $(document).ready(function() {
            // 1. Fungsi untuk mengatur visibilitas kolom
            function setColumnVisibility(colIndex, isVisible) {
                var realIndex = parseInt(colIndex) + 1; // Menyesuaikan index kolom HTML
                var cells = $('table.table').find('th:nth-child(' + realIndex + '), td:nth-child(' + realIndex +
                    ')');

                if (isVisible) {
                    cells.show();
                } else {
                    cells.hide();
                }
            }

            // 2. Load status dari LocalStorage saat halaman dimuat
            $('.toggle-vis').each(function() {
                var colId = $(this).val();
                // Ambil status tersimpan, defaultnya 'false' (sembunyi) sesuai CSS Anda
                var isChecked = localStorage.getItem('sparepart_vis' + colId) === 'true';

                $(this).prop('checked', isChecked);
                setColumnVisibility(colId, isChecked);
            });

            // 3. Simpan status ke LocalStorage saat checkbox diubah
            $('.toggle-vis').on('change', function() {
                var colId = $(this).val();
                var isChecked = $(this).is(':checked');

                // Simpan status ke localStorage
                localStorage.setItem('sparepart_vis' + colId, isChecked);

                // Jalankan fungsi sembunyi/tampil
                setColumnVisibility(colId, isChecked);
            });
        });
    </script>

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
                source: "{{ route('spare-parts.autocomplete') }}",
                minLength: 2, // mulai search setelah 2 karakter
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            const oldCategoryId = "{{ request('category_id') }}";
            const oldSubcategoryId = "{{ request('subcategory_id') }}";

            function loadSubcategories(categoryId, selectedId = null) {
                $('#subcategory_id').html('<option value="">-- Pilih Sub Category --</option>');

                if (!categoryId) return;

                $.get('/get-subcategories/' + categoryId, function(data) {
                    $.each(data, function(i, subcat) {
                        $('#subcategory_id').append(
                            `<option value="${subcat.id}" ${
                        selectedId == subcat.id ? 'selected' : ''
                    }>${subcat.name}</option>`
                        );
                    });
                });
            }

            // Saat category diubah manual
            $('#category_id').on('change', function() {
                loadSubcategories(this.value);
            });

            // Saat halaman reload (search / pagination)
            if (oldCategoryId) {
                $('#category_id').val(oldCategoryId);
                loadSubcategories(oldCategoryId, oldSubcategoryId);
            }

        });
    </script>

    <script>
        $(document).ready(function() {
            // ... kode toggle-vis yang sebelumnya ...

            // Fungsi untuk tombol Reset
            $('#btnReset').on('click', function(e) {
                // 1. Hapus semua data kolom yang tersimpan di browser
                $('.toggle-vis').each(function() {
                    var colId = $(this).val();
                    localStorage.removeItem('sparepart_vis' + colId);
                });

                // 2. Uncheck semua checkbox secara visual
                $('.toggle-vis').prop('checked', false);

                // Catatan: Karena tombol ini adalah link (<a>), 
                // halaman akan otomatis reload ke index dan kolom akan kembali tersembunyi (default CSS).
            });
        });
    </script>

    {{-- <script>
        $(document).ready(function() {
            $('.toggle-vis').on('change', function() {
                var colIndex = parseInt($(this).val()) + 1;
                // Gunakan selector yang lebih spesifik ke tabel sparepart
                var cells = $('table.table').find('th:nth-child(' + colIndex + '), td:nth-child(' +
                    colIndex + ')');

                if ($(this).is(':checked')) {
                    cells.show();
                } else {
                    cells.hide();
                }
            });
        });
    </script> --}}
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
