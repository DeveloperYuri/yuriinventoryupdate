@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <a href="{{ route('asset-tools.create') }}" class="btn btn-primary">Tambah Asset Tools</a>
            @endif

            <a href="{{ route('card-list-asset-tools.index') }}" class="btn btn-secondary"><i class="bi bi-card-list"></i></a>

        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->name }}"
                            placeholder="Searching Asset" name="name">
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

                                <h5 class="card-title">Daftar Asset Tools</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">

                                        <a href="{{ route('assettools.cetakpdf') }}" class="btn btn-success"
                                            target="_blank">Print
                                            PDF</a>
                                        <a href="{{ route('assettools.export') }}" class="btn btn-success">Export Excel</a>
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
                                            <th class="text-center">Gambar</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Harga</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Satuan</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif

                                        </tr>

                                    </thead>
                                    <tbody>
                                        @foreach ($getRecord as $index => $asset)
                                            <tr>
                                                <td class="text-center">{{ $getRecord->firstItem() + $index }}</td>
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
                                                {{-- <td class="text-center">
                                                    @if ($asset->image)
                                                        <img src="{{ asset('images/' . $asset->image) }}"
                                                            alt="{{ $asset->name }}" width="60">
                                                    @else
                                                        <span class="text-muted">Tidak ada</span>
                                                    @endif
                                                </td> --}}
                                                <td class="text-center">{{ $asset->name }}</td>
                                                <td class="text-center">Rp {{ number_format($asset->price, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">{{ $asset->stock }}</td>
                                                <td class="text-center">{{ $asset->satuan }}</td>


                                                <td class="text-center">
                                                    @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                        <a href="{{ route('asset-tools.edit', $asset->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">Edit</a>
                                                    @endif
                                                    @if (Auth::user()->is_role == 2)
                                                        <form action="{{ route('asset-tools.destroy', $asset->id) }}"
                                                            method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger mt-1"
                                                                onclick="confirmDelete(this.form)">Hapus</button>
                                                        </form>

                                                        <a href="{{ route('assettoolsdetail.history', ['id' => $asset->id]) }}"
                                                            class="btn btn-info btn-sm mt-1">
                                                            History Detail
                                                        </a>
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
