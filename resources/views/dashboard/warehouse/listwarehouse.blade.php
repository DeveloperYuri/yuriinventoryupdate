@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
            <div class="pagetitle">
                <a href="{{ route('createwarehouse') }}" class="btn btn-primary">Create New Warehouse</a>
            </div><!-- End Page Title -->
        @endif

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->name }}"
                            placeholder="Searching Warehouse" name="name">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row mt-4">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">List Warehouse</h5>

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
                                            <th class="text-center" scope="col">No</th>
                                            <th class="text-center" scope="col">Name</th>
                                            <th class="text-center" scope="col">Address</th>
                                            <th class="text-center" scope="col">PIC</th>
                                            @if (Auth::user()->is_role == 2)
                                                <th class="text-center" scope="col">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($getRecord as $key => $wrh)
                                            <tr>
                                                <th class="text-center" scope="row">{{ $getRecord->firstItem() + $key }}
                                                </th>
                                                <td class="text-center">{{ $wrh->name }}</td>
                                                <td class="text-center">{{ $wrh->address }}</td>
                                                <td class="text-center">{{ $wrh->pic }}</td>

                                                @if (Auth::user()->is_role == 2)
                                                    <td class="text-center">
                                                        <form action="{{ route('deletewarehouse', $wrh->id) }}"
                                                            method="POST">

                                                            <a href="{{ route('editwarehouse', $wrh->id) }}"
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
