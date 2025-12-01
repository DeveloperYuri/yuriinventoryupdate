@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <a href="{{ route('spare-parts.create') }}" class="btn btn-primary">Tambah Spare Part</a>
            @endif

            <a href="{{ route('card-list-spare-parts.index') }}" class="btn btn-secondary"><i class="bi bi-card-list"></i></a>
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col-4">
                        <input type="text" id="searchingtitle" name="name" class="form-control" value="{{ request('name') }}"
                            placeholder="Nama Spare Part">

                            
                    </div>
                    <div class="col-3">
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
                        <select id="subcategory_id" name="subcategory_id"
                            class="form-control @error('subcategory_id') is-invalid @enderror">
                            <option value="">-- Pilih Sub Category --</option>
                            {{-- jika ada subcategories awal (misal edit), bisa looping di sini --}}

                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">Reset</a>
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
                                        </div>

                                        <a href="{{ route('sparepart.cetakpdf') }}" class="btn btn-success"
                                            target="_blank">Print PDF</a>
                                        <a href="{{ route('sparepart.export') }}" class="btn btn-success">Export Excel</a>
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
                                            <th class="text-center">Serial Number</th>
                                            <th class="text-center">Nama</th>
                                            @if (Auth::user()->is_role == 2)
                                                <th class="text-center">Harga</th>
                                            @endif

                                            <th class="text-center">Kategori</th>
                                            <th class="text-center">Sub Kategori</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Satuan</th>


                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif
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
                                                @if (Auth::user()->is_role == 2)
                                                    <td class="text-center">Rp
                                                        {{ number_format($part->price, 0, ',', '.') }}
                                                    </td>
                                                @endif

                                                <td class="text-center">{{ $part->category->name ?? '-' }}</td>
                                                <td class="text-center">{{ $part->subcategory->name ?? '-' }}</td>
                                                <td class="text-center">{{ $part->stock }}</td>

                                                <td class="text-center">{{ $part->satuan }}</td>


                                                <td class="text-center">
                                                    @if (Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
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
