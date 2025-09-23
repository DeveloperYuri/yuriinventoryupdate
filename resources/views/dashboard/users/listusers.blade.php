@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <a href="{{ route('createusers') }}" class="btn btn-primary">Create New Users</a>
        </div><!-- End Page Title -->

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control"
                            value="{{ Request()->name }}" placeholder="Searching Users" name="name">
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
                            <h5 class="card-title">List Users</h5>

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
                                            <th class="text-center" scope="col">Email</th>
                                            <th class="text-center" scope="col">Role</th>
                                            <th class="text-center" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($getRecord as $u)
                                            <tr>
                                                <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                <td class="text-center">{{ $u->name }}</td>
                                                <td class="text-center">{{ $u->email }}</td>
                                                <td class="text-center">
                                                    @if ($u->is_role == '0')
                                                        Users
                                                    @elseif ($u->is_role == '1')
                                                        Admin
                                                    @else
                                                        Super Admin
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('deleteusers', $u->id) }}" method="POST">

                                                        <a href="{{ route('editusers', $u->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">EDIT</a>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger mt-1"
                                                            onclick="confirmDelete(this.form)">HAPUS</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="100%">Users Not Found</td>
                                            </tr>
                                        @endforelse


                                    </tbody>
                                </table>
                                <!-- End Default Table Example -->
                            </div>

                            <div class="d-flex justify-content-center">
                                {{ $getRecord->links() }}
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

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
