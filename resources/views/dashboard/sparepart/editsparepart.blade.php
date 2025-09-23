@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Spare Part</h5>

                            <form method="POST" action="{{ route('spare-parts.update', $sparePart->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">IMAGE</label>
                                    <div class="col-sm-10">
                                        @if ($sparePart->image)
                                            <img src="{{ asset('images/' . $sparePart->image) }}"
                                                alt="{{ $sparePart->name }}" width="100">
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                        <br>
                                        <label class="mt-2">Ganti Gambar (Opsional)</label>
                                        <input type="file" name="image"
                                            class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Serial Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="numbers" class="form-control"
                                            value="{{ $sparePart->numbers }}">

                                        {{-- @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ $sparePart->name }}">

                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if (Auth::user()->is_role == 2)
                                    <div class="row mb-3">
                                        <label for="inputEmail3" class="col-sm-2 col-form-label">Price</label>
                                        <div class="col-sm-10">
                                            <input type="number" name="price"
                                                class="form-control @error('price') is-invalid @enderror"
                                                value="{{ $sparePart->price }}">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select name="satuan" class="form-control" required dusk="sparepartintoggle"
                                            value="{{ $sparePart->satuan }}">
                                            <option value="Pcs" {{ $sparePart->satuan == 'Pcs' ? 'selected' : '' }}>Pcs
                                            </option>
                                            <option value="Pack" {{ $sparePart->satuan == 'Pack' ? 'selected' : '' }}>
                                                Pack
                                            </option>
                                            <option value="Meter" {{ $sparePart->satuan == 'Meter' ? 'selected' : '' }}>
                                                Meter</option>
                                            <option value="Set" {{ $sparePart->satuan == 'Set' ? 'selected' : '' }}>
                                                Set</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">Back</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
