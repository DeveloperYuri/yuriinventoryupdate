@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Perbaikan Asset IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('perbaikanasset-it.store') }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Foto<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            name="image">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nomor Asset<span
                                            style="color:red">*</span></label>
                                    <div class="col-sm-10">
                                        <select id="asset_id" name="asset_id" class="form-control">
                                            <option value="">-- Pilih Asset --</option>
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->id }}">
                                                    {{ $asset->nomer_asset }} - {{ $asset->nama_asset }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}


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
                                    <label class="col-sm-2 col-form-label">Nomer Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset" name="nomer_asset" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nama Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nama" name="nama" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Digunakan Oleh</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="user" name="user" class="form-control">
                                    </div>
                                </div>

                                {{-- <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Lokasi</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="lokasi" name="lokasi" class="form-control" readonly>
                                    </div>
                                </div> --}}

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
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Kerusakan <span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('kerusakan') is-invalid @enderror"
                                            id="inputText" name="kerusakan" value="{{ old('kerusakan') }}">
                                        @error('kerusakan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Perbaikan<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('perbaikan') is-invalid @enderror"
                                            id="inputText" name="perbaikan" value="{{ old('perbaikan') }}">
                                        @error('perbaikan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Tanggal Mulai<span
                                            style="color:red">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="tanggalMulai" name="tanggal" type="text" class="form-control"
                                            placeholder="Pilih tanggal..." autocomplete="off"
                                            value="{{ now()->format('Y-m-d') }}">
                                        <input type="hidden" name="tanggal_mulai" id="tanggalHidden"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Tanggal Selesai
                                    </label>

                                    <div class="col-sm-8">
                                        <!-- Input tampilan (kosong) -->
                                        <input id="tanggalSelesai" type="text" class="form-control"
                                            placeholder="Pilih tanggal..." autocomplete="off">

                                        <!-- Input yang dikirim ke controller -->
                                        <input type="hidden" name="tanggal_selesai" id="tanggalSelesaiHidden">
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Status<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="status" class="form-control">
                                            <option value="Sedang Perbaikan">Sedang Perbaikan</option>
                                            <option value="Selesai">Selesai</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="keterangan" class="col-sm-2 col-form-label">
                                        Keterangan
                                    </label>

                                    <div class="col-sm-10">
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                            rows="4">{{ old('keterangan') }}</textarea>

                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('perbaikanasset-it.index') }}"
                                            class="btn btn-secondary">Kembali</a>
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
    {{-- <script>
        document.getElementById('asset_id').addEventListener('change', function() {
            let assetId = this.value;

            if (!assetId) {
                document.getElementById('nama_asset').value = '';
                document.getElementById('digunakan_oleh').value = '';
                document.getElementById('lokasi').value = '';
                return;
            }

            fetch("{{ route('asset-it.ajax-detail') }}?asset_id=" + assetId)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    console.log(test);
                    document.getElementById('nama_asset').value = data.nama_asset;
                    document.getElementById('digunakan_oleh').value = data.user;
                    document.getElementById('lokasi').value = data.lokasi;
                })
                .catch(err => console.error(err));
        });
    </script> --}}

    <script>
        new Litepicker({
            element: document.getElementById('tanggalMulai'),
            lang: 'id', // Bahasa Indonesia
            format: 'DD MMMM YYYY', // 29 November 2025
            dropdowns: {
                minYear: 2020,
                maxYear: new Date().getFullYear() + 5,
                months: true,
                years: true
            },
            setup: (picker) => {
                picker.on('selected', (date) => {
                    const mysql = date.format('YYYY-MM-DD');
                    document.getElementById('tanggalHidden').value = mysql;
                });
            }
        });

        new Litepicker({
            element: document.getElementById('tanggalSelesai'),
            lang: 'id', // Bahasa Indonesia
            format: 'DD MMMM YYYY', // 29 November 2025
            dropdowns: {
                minYear: 2020,
                maxYear: new Date().getFullYear() + 5,
                months: true,
                years: true
            },
            setup: (picker) => {
                picker.on('selected', (date) => {
                    const mysql = date.format('YYYY-MM-DD');
                    document.getElementById('tanggalSelesaiHidden').value = mysql;
                });
            }
        });
    </script>
@endpush
