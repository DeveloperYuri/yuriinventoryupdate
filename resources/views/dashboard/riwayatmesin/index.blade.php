@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
            <div class="pagetitle">
                <a href="{{ route('create.riwayatmesin') }}" class="btn btn-primary">Tambah Riwayat Mesin</a>
            </div><!-- End Page Title -->
        @endif

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->nama_mesin }}"
                            placeholder="Searching Riwayat Mesin" name="nama_mesin">
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
                            <h5 class="card-title">Daftar Riwayat Mesin</h5>

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
                                            <th class="text-center" scope="col">Tanggal</th>
                                            <th class="text-center" scope="col">Kategori Mesin</th>
                                            <th class="text-center" scope="col">Sub Kategori Mesin</th>
                                            <th class="text-center" scope="col">Pekerjaan</th>
                                            <th class="text-center" scope="col">PIC</th>
                                            <th class="text-center" scope="col">Status</th>

                                            @if (Auth::user()->is_role == 2)
                                                <th class="text-center" scope="col">Action</th>
                                            @endif

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($getRecord as $key => $data)
                                            <tr>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                                                <td class="text-center">{{ $data->category->name ?? '-' }}</td>
                                                <td class="text-center">{{ $data->subcategory->name ?? '-' }}
                                                </td>
                                                <td class="text-center">{{ $data->pekerjaan }}</td>
                                                <td class="text-center">{{ $data->pic }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $badgeColor = 'secondary'; // default

                                                        if ($data->status == 'Selesai') {
                                                            $badgeColor = 'success';
                                                        } elseif ($data->status == 'Pending') {
                                                            $badgeColor = 'warning';
                                                        } elseif ($data->status == 'Rusak') {
                                                            $badgeColor = 'danger';
                                                        }
                                                    @endphp

                                                    <span class="badge bg-{{ $badgeColor }}">{{ $data->status }}</span>
                                                </td>


                                                @if (Auth::user()->is_role == 2)
                                                    <td class="text-center">
                                                        <form action="{{ route('delete.riwayatmesin', $data->id) }}"
                                                            method="POST">

                                                            <a href="{{ route('show.riwayatmesin', $data->id) }}"
                                                                class="btn btn-sm btn-info mt-1">Detail</a>

                                                            <a href="{{ route('edit.riwayatmesin', $data->id) }}"
                                                                class="btn btn-sm btn-warning mt-1">EDIT</a>

                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger mt-1"
                                                                onclick="confirmDelete(this.form)">HAPUS</button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="100%">Tidak Ada Riwayat Mesin</td>
                                            </tr>
                                        @endforelse


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

                            <div style="padding: 10px; float: right;">
                                {!! $getRecord->appends(Illuminate\Support\Facades\Request::except('page'))->links() !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
