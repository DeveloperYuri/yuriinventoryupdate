@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Spare Part</h5>

                            <form method="POST" action="{{ route('atk.update', $atk->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">IMAGE</label>
                                    <div class="col-sm-10">
                                        @if ($atk->image)
                                            <img src="{{ asset('images/' . $atk->image) }}"
                                                alt="{{ $atk->name }}" width="100">
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
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ $atk->name }}">

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
                                                value="{{ $atk->price }}">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select name="satuan" class="form-control" required dusk="atkintoggle"
                                            value="{{ $atk->satuan }}">
                                            <option value="Pcs" {{ $atk->satuan == 'Pcs' ? 'selected' : '' }}>Pcs
                                            </option>
                                            <option value="Pack" {{ $atk->satuan == 'Pack' ? 'selected' : '' }}>
                                                Pack
                                            </option>
                                            <option value="Meter" {{ $atk->satuan == 'Meter' ? 'selected' : '' }}>
                                                Meter</option>
                                            <option value="Set" {{ $atk->satuan == 'Set' ? 'selected' : '' }}>
                                                Set</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('atk.index') }}" class="btn btn-secondary">Back</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
