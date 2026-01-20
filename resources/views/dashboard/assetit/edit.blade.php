@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Asset IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('asset-it.update', $assetit->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @method('PUT')
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nomer Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control @error('nomer_asset') is-invalid @enderror" id="inputText"
                                            name="nomer_asset" value="{{ $assetit->nomer_asset }}">
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
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Foto Asset</label>
                                    <div class="col-sm-10">
                                        @if ($assetit->image)
                                            <img src="{{ asset('images/' . $assetit->image) }}" alt="{{ $assetit->name }}"
                                                width="100">
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
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="inputText" name="nama" value="{{ $assetit->nama }}">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Digunakan oleh</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('user') is-invalid @enderror"
                                            id="inputText" name="user" value="{{ $assetit->user }}">
                                        @error('user')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Lokasi<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="locations_id" class="form-control ...">
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('locations_id', $assetit->locations_id) == $location->id ? 'selected' : '' }}>
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
                                        Spesifikasi <span style="color: red">*</span>
                                    </label>

                                    <div class="col-sm-10">
                                        <textarea class="form-control @error('spesifikasi') is-invalid @enderror" id="spesifikasi" name="spesifikasi"
                                            rows="14">{{ $assetit->spesifikasi }}</textarea>

                                        @error('spesifikasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-10">
                                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value="Tersedia"
                                                {{ old('status', $assetit->status) == 'Tersedia' ? 'selected' : '' }}>
                                                Tersedia</option>
                                            <option value="Dipakai"
                                                {{ old('status', $assetit->status) == 'Dipakai' ? 'selected' : '' }}>
                                                Dipakai
                                            </option>
                                            <option value="Rusak"
                                                {{ old('status', $assetit->status) == 'Rusak' ? 'selected' : '' }}>
                                                Rusak
                                            </option>
                                        </select>

                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
