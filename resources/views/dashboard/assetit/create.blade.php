@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Baru Asset IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('asset-it.store') }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nomer Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control @error('nomer_asset') is-invalid @enderror" id="inputText"
                                            name="nomer_asset" value="{{ old('nomer_asset') }}">
                                        @error('nomer_asset')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- generate otomatis numer asset IT --}}
                                {{-- <div class="row mb-3">
                                    <label for="nomor_asset" class="col-sm-2 col-form-label">Nomor Asset</label>
                                    <div class="col-sm-10">
                                        <!-- Tampil readonly di form -->
                                        <input type="text" class="form-control" id="nomor_asset"
                                            value="{{ $newNumber }}" readonly>

                                        <!-- Input yang dikirim ke controller -->
                                        <input type="hidden" name="nomor_asset" value="{{ $newNumber }}">

                                        @error('nomor_asset')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}


                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Foto Asset<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            name="image">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="inputText" name="nama" value="{{ old('nama') }}">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Digunakan oleh</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('user') is-invalid @enderror"
                                            id="inputText" name="user" value="{{ old('user') }}">
                                        @error('user')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Lokasi<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="locations_id"
                                            class="form-control @error('locations_id') is-invalid @enderror">
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('locations_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('locations_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="spesifikasi" class="col-sm-2 col-form-label">
                                        Spesifikasi
                                    </label>

                                    <div class="col-sm-10">
                                        <textarea class="form-control @error('spesifikasi') is-invalid @enderror" id="spesifikasi" name="spesifikasi"
                                            rows="4">{{ old('spesifikasi') }}</textarea>

                                        @error('spesifikasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-10">
                                        <select name="status" class="form-control">
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Dipakai">Dipakai</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Sedang Perbaikan">Sedang Perbaikan</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('asset-it.index') }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                </div>
                            </form><!-- End Horizontal Form -->

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
@endsection
