@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                <a href="{{ route('asset-tools.create') }}" class="btn btn-primary">Add Asset Tools</a>
            @endif

            <a href="{{ route('asset-tools.index') }}" class="btn btn-secondary"><i class="bi bi-list"></i></a>
        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control" value="{{ Request()->name }}"
                            placeholder="Searching Asset" name="name">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <section class="section">

            <div class="row">

                @foreach ($getRecordCard as $index => $asset)
                    <div class="col-lg-4 mt-3">
                        <!-- Card with titles, buttons, and links -->
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mt-3" style="height: 100px;">
                                    @if ($asset->image)
                                        <img src="{{ asset('images/' . $asset->image) }}" alt="{{ $asset->name }}"
                                            style="width: 200px; height: 80px; object-fit: contain;">
                                    @else
                                        <div class="text-muted d-flex align-items-center justify-content-center"
                                            style="height: 80px;">
                                            Tidak ada
                                        </div>
                                    @endif
                                </div>

                                <h5 class="card-title text-center">{{ $asset->name }}</h5>
                                <p class="card-text mb-1">Rp {{ number_format($asset->price, 0, ',', '.') }}</p>
                                <p class="card-text">Stock : {{ $asset->stock }}</p>
                                @if (Auth::user()->is_role == 2)
                                    <div class="d-flex flex-wrap gap-2 mt-2 align-items-center">
                                        <a href="{{ route('assettoolsdetail.history', ['id' => $asset->id]) }}"
                                            class="btn btn-info btn-sm">
                                            History Detail
                                        </a>

                                        <a href="{{ route('asset-tools.edit', $asset->id) }}"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="{{ route('asset-tools.destroy', $asset->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete(this.form)">
                                                Hapus
                                            </button>
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
