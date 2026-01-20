@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Peminjaman Asset IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('peminjamanasset-it.store') }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                {{-- <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Nomer Asset <span style="color:red">*</span>
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="text" id="asset_autocomplete" class="form-control"
                                            placeholder="Ketik nomor / nama asset" autocomplete="off" required>

                                        <input type="hidden" name="asset_id" id="asset_id">
                                    </div>
                                </div> --}}


                                {{-- Ajax Jalan --}}
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Nomer Asset <span style="color: red">*</span>
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset" name="nomer_asset" class="form-control"
                                            list="assetList" placeholder="Ketik / pilih nomer asset" required>

                                        <datalist id="assetList">
                                            @foreach ($assets as $asset)
                                                <option value="{{ $asset->nomer_asset }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>


                                {{-- <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nomer Asset<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset" name="nomer_asset" class="form-control">
                                    </div>
                                </div> --}}

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Nama Asset<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="nama" name="nama" class="form-control">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Digunakan Oleh<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="user" name="user" class="form-control">
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
                                    <label class="col-sm-2 col-form-label">Tanggal Pinjam<span
                                            style="color:red">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="tanggalMulai" name="tanggal" type="text" class="form-control"
                                            placeholder="Pilih tanggal..." autocomplete="off"
                                            value="{{ now()->format('Y-m-d') }}">
                                        <input type="hidden" name="tanggal_pinjam" id="tanggalHidden"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Tanggal Kembali
                                    </label>

                                    <div class="col-sm-8">
                                        <!-- Input tampilan (kosong) -->
                                        <input id="tanggalSelesai" type="text" class="form-control"
                                            placeholder="Pilih tanggal..." autocomplete="off">

                                        <!-- Input yang dikirim ke controller -->
                                        <input type="hidden" name="tanggal_kembali" id="tanggalSelesaiHidden">
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
        $(function() {
            $("#asset_autocomplete").autocomplete({
                minLength: 2,
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('assetit.autocomplete') }}",
                        dataType: "json",
                        data: {
                            q: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    $('#asset_autocomplete').val(ui.item.label);
                    $('#asset_id').val(ui.item.id);

                    // 🔥 PANGGIL AJAX DETAIL DI SINI
                    loadAssetDetail(ui.item.id);

                    return false;
                }
            });
        });

        // AJAX DETAIL
        function loadAssetDetail(assetId) {
            $.ajax({
                url: "{{ route('asset-it.ajax-detail') }}",
                type: "GET",
                dataType: "json",
                data: {
                    asset_id: assetId
                },
                success: function(data) {
                    if (!data) return;

                    $('#nama').val(data.nama);
                    $('#user').val(data.user);
                    $('select[name="locations_id"]').val(data.location_id);
                },
                error: function(err) {
                    console.error(err);
                }
            });
        }
    </script> --}}

    {{-- // Ajax Jalan --}}
    <script>
        document.getElementById('nomer_asset').addEventListener('change', function() {
            let nomerAsset = this.value;
            if (!nomerAsset) return;

            fetch("{{ route('asset-it.ajax-detail') }}?nomer_asset=" + nomerAsset)
                .then(response => response.json())
                .then(data => {
                    if (!data) return;

                    document.getElementById('nama').value = data.nama;
                    document.getElementById('user').value = data.user;
                    document.querySelector('select[name="locations_id"]').value = data.location_id;
                })
                .catch(err => console.error(err));
        });
    </script>


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
