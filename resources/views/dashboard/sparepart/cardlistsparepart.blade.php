@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

            <div class="pagetitle d-flex justify-content-between align-items-center">
                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                 <a href="{{ route('spare-parts.create') }}" class="btn btn-primary">Add Spare Part</a>
                @endif

                <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary"><i class="bi bi-list"></i></a>
            </div>

            <div class="mt-4">
                <form method="get">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <input id="searchingtitle" type="text" class="form-control"
                                value="{{ Request()->name }}" placeholder="Searching Spare Part" name="name">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-dark">Search</button>
                        </div>
                    </div>
                </form>
            </div>

        <section class="section">

            <div class="row">

                @foreach ($getRecordCard as $index => $part)
                    <div class="col-lg-4 mt-3">
                        <!-- Card with titles, buttons, and links -->
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mt-3" style="height: 100px;">
                                    @if ($part->image)
                                        <img src="{{ asset('images/' . $part->image) }}" alt="{{ $part->name }}"
                                            style="width: 200px; height: 80px; object-fit: contain;">
                                    @else
                                        <div class="text-muted d-flex align-items-center justify-content-center"
                                            style="height: 80px;">
                                            Tidak ada
                                        </div>
                                    @endif
                                </div>

                                <h5 class="card-title text-center">{{ $part->name }}</h5>
                                <p class="card-text mb-1">Price : Rp {{ number_format($part->price, 0, ',', '.') }}</p>
                                <p class="card-text">Stock : {{ $part->stock }}</p>
                                @if (Auth::user()->is_role == 2)

                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('sparepartdetail.history', ['id' => $part->id]) }}" class="btn btn-info btn-sm">
                                        History Detail
                                    </a>
                                    
                                    <a href="{{ route('spare-parts.edit', $part->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>

                                    <form action="{{ route('spare-parts.destroy', $part->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete(this.form)">Hapus</button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div><!-- End Card with titles, buttons, and links -->

                    </div>
                @endforeach
            </div>

            <!-- PAGINATION LINK -->
            <div class="d-flex justify-content-center mt-5">
                {{ $getRecordCard->links() }}
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

        </section>


    </main><!-- End #main -->
@endsection
