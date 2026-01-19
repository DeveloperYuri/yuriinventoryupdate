@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Peminjaman Asset IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm"
                                action="{{ route('peminjamanasset-it.update', $riwayatpeminjamanassetit->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nomer Asset<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset" name="nomer_asset" class="form-control"
                                            value="{{ $riwayatpeminjamanassetit->nomer_asset }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nama Asset<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nama" name="nama" class="form-control"
                                            value="{{ $riwayatpeminjamanassetit->nama }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Digunakan Oleh<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="user" name="user" class="form-control"
                                            value="{{ $riwayatpeminjamanassetit->user }}">
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
                                                    {{ old('locations_id', $riwayatpeminjamanassetit->locations_id) == $location->id ? 'selected' : '' }}>
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
                                    <label class="col-sm-2 col-form-label">
                                        Tanggal Pinjam<span style="color:red">*</span>
                                    </label>

                                    <div class="col-sm-8">
                                        <!-- INPUT TAMPILAN -->
                                        <input id="tanggalMulai" type="text" class="form-control" autocomplete="off"
                                            value="{{ \Carbon\Carbon::parse($riwayatpeminjamanassetit->tanggal_pinjam)->translatedFormat('d F Y') }}">

                                        <!-- INPUT KE DB -->
                                        <input type="hidden" name="tanggal_pinjam" id="tanggalMulaiHidden"
                                            value="{{ $riwayatpeminjamanassetit->tanggal_pinjam }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Tanggal Kembali
                                    </label>

                                    <div class="col-sm-10">
                                        <!-- INPUT TAMPILAN -->
                                        <input id="tanggalSelesai" type="text" class="form-control" autocomplete="off" placeholder="Pilih tanggal..."
                                            value="{{ $riwayatpeminjamanassetit->tanggal_kembali
                                                ? \Carbon\Carbon::parse($riwayatpeminjamanassetit->tanggal_kembali)->translatedFormat('d F Y')
                                                : '' }}">

                                        <!-- INPUT KE DB -->
                                        <input type="hidden" name="tanggal_kembali" id="tanggalSelesaiHidden"
                                            value="{{ $riwayatpeminjamanassetit->tanggal_kembali ?? '' }}">

                                        {{-- <input type="hidden" name="tanggal_kembali" id="tanggalSelesaiHidden"> --}}
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Status<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select name="status" class="form-control">
                                            <option value="Di Pinjam">Di Pinjam</option>
                                            <option value="Kembali">Kembali</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="keterangan" class="col-sm-2 col-form-label">
                                        Keterangan
                                    </label>

                                    <div class="col-sm-10">
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                            rows="4">{{ $riwayatpeminjamanassetit->keterangan }}</textarea>

                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('peminjamanasset-it.index') }}"
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
            // defaultDate: document.getElementById('tanggalMulai').value,
            dropdowns: {
                minYear: 2020,
                maxYear: new Date().getFullYear() + 5,
                months: true,
                years: true
            },
            setup: (picker) => {
                picker.on('selected', (date) => {
                    const mysql = date.format('YYYY-MM-DD');
                    document.getElementById('tanggalMulaiHidden').value = mysql;
                });
            }
        });

        new Litepicker({
            element: document.getElementById('tanggalSelesai'),
            lang: 'id', // Bahasa Indonesia
            format: 'DD MMMM YYYY', // 29 November 2025
            // defaultDate: document.getElementById('tanggalSelesai').value,
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
