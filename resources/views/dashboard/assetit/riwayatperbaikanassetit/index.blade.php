@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 3 || Auth::user()->is_role == 2)
                <a href="{{ route('perbaikanasset-it.create') }}" class="btn btn-primary">Tambah Perbaikan Asset IT</a>
            @endif
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->nomer_asset }}"
                            placeholder="Cari Nomer Asset IT" name="nomer_asset">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Cari</button>
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
                                <h5 class="card-title mb-0">Daftar Riwayat Perbaikan Asset IT</h5>

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
                                            <th class="text-center">Foto</th>
                                            <th class="text-center">Nomer</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Lokasi</th>
                                            <th class="text-center">Kerusakan</th>
                                            <th class="text-center">Perbaikan</th>
                                            <th class="text-center">Tanggal Mulai</th>
                                            <th class="text-center">Tanggal Selesai</th>
                                            <th class="text-center">Status</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 3)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($getRecord as $index => $asset)
                                            <tr>
                                                <td class="text-center">
                                                    @if ($asset->image)
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#imageModal{{ $asset->id }}">
                                                            <img src="{{ asset('images/' . $asset->image) }}"
                                                                class="img-thumbnail"
                                                                style="width: 100px; height: 70px; object-fit: contain;">
                                                        </a>

                                                        <!-- Modal polos -->
                                                        <div class="modal fade" id="imageModal{{ $asset->id }}"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                <div
                                                                    class="modal-content bg-transparent border-0 shadow-none">
                                                                    <div class="modal-body text-center p-0">
                                                                        <img src="{{ asset('images/' . $asset->image) }}"
                                                                            class="img-fluid rounded"
                                                                            style="max-height: 90vh;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="text-center">{{ $asset->nomer_asset }}</td>
                                                <td class="text-center">{{ $asset->nama ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->user ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->location->name ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->kerusakan ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->perbaikan ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->tanggal_mulai }}</td>
                                                <td class="text-center">{{ $asset->tanggal_selesai ?? '-' }}</td>
                                                <td class="text-center">{{ $asset->status }}</td>


                                                <td class="text-center">
                                                    @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                        <a href="{{ route('perbaikanasset-it.show', ['id' => $asset->id]) }}"
                                                            class="btn btn-info btn-sm mt-1">
                                                            Detail
                                                        </a>

                                                        <a href="{{ route('perbaikanasset-it.edit', $asset->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">Edit</a>
                                                    @endif
                                                    @if (Auth::user()->is_role == 2)
                                                        <form
                                                            action="{{ route('perbaikanasset-it.delete', $asset->id) }}"
                                                            method="POST" style="display:inline-block;">
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
