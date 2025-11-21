@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 0)
                <a href="{{ route('suratpesanan-atk.create') }}" class="btn btn-primary" dusk="addsparepart">Buat Surat
                    Pesanan ATK</a>
            @endif
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->name }}"
                            placeholder="Searching Surat Pesanan ATK" name="name">
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
                                <h5 class="card-title mb-0">Daftar Surat Pesanan</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">

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
                                                        {{-- <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Import</button>
                                                        </div> --}}
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                                            <th class="text-center">No. SP</th>
                                            <th class="text-center">Di Buat Oleh</th>
                                            <th class="text-center">Lokasi</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Status</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($getRecord as $index => $sp)
                                            <tr onclick="window.location='{{ route('suratpesanan-atk.show', $sp->id) }}'"
                                                style="cursor:pointer;">
                                                <td class="text-center">
                                                    {{ !empty($sp->no_surat_pesanan) ? $sp->no_surat_pesanan : '000-000-000' }}
                                                </td>

                                                <td class="text-center">{{ $sp->dibuat_oleh }}</td>
                                                <td class="text-center">{{ $sp->location->name ?? '-' }}</td>

                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($sp->created_at)->format('d-m-Y') }}</td>

                                                <td class="text-center">
                                                    @if ($sp->status == 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif ($sp->status == 'onprogress')
                                                        <span class="badge bg-warning">On Progress</span>
                                                    @elseif ($sp->status == 'approve')
                                                        <span class="badge bg-success">Approve</span>
                                                    @elseif ($sp->status == 'reject')
                                                        <span class="badge bg-danger">Reject</span>
                                                    @endif
                                                </td>

                                                <td class="text-nowrap">

                                                    @if ($sp->status == 'draft')
                                                        <a href="{{ route('suratpesanan-atk.edit', $sp->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>

                                                        <form action="{{ route('suratpesanan-atk.delete', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="event.stopPropagation(); confirmDelete(this.form)">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan-atk.submit', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-warning">Ajukan</button>
                                                        </form>
                                                    @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 1 || auth()->user()->is_role == 2))
                                                        <form action="{{ route('suratpesanan-atk.approve', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan-atk.reject', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    @elseif ($sp->status == 'approved' || $sp->status == 'rejected')
                                                        {{-- status final → tidak ada tombol --}}
                                                    @endif

                                                </td>


                                                {{-- <td class="text-nowrap">
                                                    @if ($sp->status == 'draft')
                                                        <a href="{{ route('suratpesanan-atk.edit', $sp->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>

                                                        <form action="{{ route('suratpesanan-atk.delete', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="event.stopPropagation(); confirmDelete(this.form)">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan-atk.submit', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-warning">Ajukan</button>
                                                        </form>
                                                    @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 1 || auth()->user()->is_role == 2))
                                                        <form action="{{ route('suratpesanan-atk.approve', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan-atk.reject', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    @endif
                                                </td> --}}
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
