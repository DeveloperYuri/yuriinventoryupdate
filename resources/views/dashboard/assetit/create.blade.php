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
                                    <label class="col-sm-2 col-form-label">Nomer Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset"
                                            class="form-control @error('nomer_asset') is-invalid @enderror"
                                            name="nomer_asset" value="{{ old('nomer_asset') }}" readonly>
                                        @error('nomer_asset')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

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
                                    <label class="col-sm-2 col-form-label">
                                        Category Asset <span style="color:red">*</span>
                                    </label>

                                    <div class="col-sm-10">
                                        <select name="nama" id="category_asset"
                                            class="form-control @error('nama') is-invalid @enderror">

                                            <option value="">-- Pilih Category --</option>

                                            @php
                                                $listNama = [
                                                    'KOMPUTER',
                                                    'PRINTER',
                                                    'LAPTOP',
                                                    'PROYEKTOR',
                                                    'INFRASTRUKTUR JARINGAN',
                                                    'PC SERVER',
                                                    'INFRASTRUKTUR TELPON',
                                                    'INFRASTRUKTUR CCTV',
                                                ];

                                                $selectedNama = old('nama', $perbaikan->nama ?? '');
                                            @endphp

                                            @foreach ($listNama as $nama)
                                                <option value="{{ $nama }}"
                                                    {{ $selectedNama == $nama ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>

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

@push('scripts')
    <script>
        document.getElementById('category_asset').addEventListener('change', function() {
            const nama = this.value;
            const inputNomor = document.getElementById('nomer_asset');

            if (!nama) {
                inputNomor.value = '';
                return;
            }

            fetch(`{{ route('assetit.generate-number') }}?nama=${encodeURIComponent(nama)}`)
                .then(response => response.json())
                .then(data => {
                    inputNomor.value = data.number ?? '';
                })
                .catch(() => {
                    inputNomor.value = '';
                });
        });
    </script>
@endpush
