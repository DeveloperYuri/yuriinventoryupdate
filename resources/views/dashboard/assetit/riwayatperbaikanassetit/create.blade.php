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

                                {{-- <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Foto<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            name="image">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">
                                        Nomer Asset <span style="color:red">*</span>
                                    </label>

                                    <div class="col-sm-10">
                                        <input type="text" id="nomer_asset" name="nomer_asset" class="form-control"
                                            placeholder="Ketik nomor asset..." autocomplete="off" required>

                                        <ul id="assetDropdown" class="dropdown-menu w-99"
                                            style="max-height: 220px; overflow-y: auto;">
                                        </ul>
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

                                {{-- <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Perbaikan</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('perbaikan') is-invalid @enderror"
                                            id="inputText" name="perbaikan" value="{{ old('perbaikan') }}">
                                        @error('perbaikan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Spare Part</label>

                                    <div class="col-sm-10">
                                        <div id="sparepart-wrapper">

                                            <div class="row g-2 sparepart-row mb-2">
                                                <div class="col-md-6 position-relative">
                                                    <!-- INPUT TEXT -->
                                                    <input type="text" class="form-control sparepart-input"
                                                        placeholder="Ketik nama spare part...">

                                                    <!-- HIDDEN ID -->
                                                    <input type="hidden" name="spareparts[0][sparepart_id]"
                                                        class="sparepart-id">

                                                    <!-- DROPDOWN -->
                                                    <ul class="dropdown-menu w-100 sparepart-dropdown"></ul>
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="number" name="spareparts[0][qty]" class="form-control"
                                                        min="1" placeholder="Qty">
                                                </div>

                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-danger remove-sparepart">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>


                                            {{-- <div class="row g-2 sparepart-row mb-2">
                                                <div class="col-md-6">
                                                    <select name="spareparts[0][sparepart_id]" class="form-control">
                                                        <option value="">-- Pilih Spare Part --</option>
                                                        @foreach ($spareparts as $sp)
                                                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <input type="number" name="spareparts[0][qty]" class="form-control"
                                                        placeholder="Qty">
                                                </div>

                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-danger remove-sparepart">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div> --}}

                                        </div>

                                        <button type="button" id="add-sparepart" class="btn btn-sm btn-success">
                                            + Tambah Spare Part
                                        </button>
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
    <script>
        const input = document.getElementById('nomer_asset');
        const dropdown = document.getElementById('assetDropdown');

        let timer = null;

        // 🔹 fetch & show dropdown
        input.addEventListener('input', function() {
            const val = this.value.trim();

            if (val.length < 2) {
                dropdown.classList.remove('show');
                return;
            }

            clearTimeout(timer);
            timer = setTimeout(() => {
                fetch(`{{ route('asset-it.suggest') }}?q=${val}`)
                    .then(res => res.json())
                    .then(data => {
                        dropdown.innerHTML = '';

                        if (!data.length) {
                            dropdown.classList.remove('show');
                            return;
                        }

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                        <a class="dropdown-item" href="#">${item}</a>
                    `;

                            li.onclick = (e) => {
                                e.preventDefault();
                                input.value = item;
                                dropdown.classList.remove('show');
                                loadAssetDetail(item);
                            };

                            dropdown.appendChild(li);
                        });

                        dropdown.classList.add('show');
                    });
            }, 250);
        });

        // 🔹 klik luar = tutup
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                dropdown.classList.remove('show');
            }
        });

        // 🔹 ENTER / PASTE tetap jalan
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                dropdown.classList.remove('show');
                loadAssetDetail(this.value);
            }
        });

        // 🔹 BLUR (paste mouse)
        input.addEventListener('blur', function() {
            setTimeout(() => {
                dropdown.classList.remove('show');
                loadAssetDetail(this.value);
            }, 150);
        });

        // 🔹 function lama (TETAP)
        function loadAssetDetail(nomerAsset) {
            if (!nomerAsset) return;

            fetch(`{{ route('asset-it.ajax-detail') }}?nomer_asset=${nomerAsset}`)
                .then(res => res.json())
                .then(data => {
                    if (!data) return;

                    document.getElementById('nama').value = data.nama;
                    document.getElementById('user').value = data.user;
                    document.querySelector('select[name="locations_id"]').value = data.location_id;
                });
        }
    </script>

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
        const sparepartOptions = `
        <option value="">-- Pilih Spare Part --</option>
        @foreach ($spareparts as $sp)
            <option value="{{ $sp->id }}">
                {{ $sp->name }}
            </option>
        @endforeach
    `;
    </script>

    <script>
        let index = 1;

        document.getElementById('add-sparepart').addEventListener('click', function() {
            const wrapper = document.getElementById('sparepart-wrapper');

            const html = `
    <div class="row g-2 sparepart-row mb-2">
        <div class="col-md-6 position-relative">
            <input type="text"
                   class="form-control sparepart-input"
                   placeholder="Ketik nama spare part...">

            <input type="hidden"
                   name="spareparts[${index}][sparepart_id]"
                   class="sparepart-id">

            <ul class="dropdown-menu w-100 sparepart-dropdown"></ul>
        </div>

        <div class="col-md-3">
            <input type="number"
                   name="spareparts[${index}][qty]"
                   class="form-control"
                   min="1"
                   placeholder="Qty">
        </div>

        <div class="col-md-3">
            <button type="button" class="btn btn-danger remove-sparepart">
                Hapus
            </button>
        </div>
    </div>`;

            wrapper.insertAdjacentHTML('beforeend', html);
            index++;
        });
    </script>

    <script>
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('sparepart-input')) return;

            const input = e.target;
            const dropdown = input.closest('.position-relative')
                .querySelector('.sparepart-dropdown');

            const keyword = input.value.trim();

            if (keyword.length < 2) {
                dropdown.classList.remove('show');
                return;
            }

            fetch(`{{ route('perbaikan-it.sparepart.autocomplete') }}?q=${keyword}`)
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = '';

                    if (!data.length) {
                        dropdown.classList.remove('show');
                        return;
                    }

                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                    <a class="dropdown-item" href="#">
                        ${item.name}
                    </a>
                `;

                        li.onclick = (e) => {
                            e.preventDefault();

                            input.value = item.name;
                            input.closest('.position-relative')
                                .querySelector('.sparepart-id').value = item.id;

                            dropdown.classList.remove('show');
                        };

                        dropdown.appendChild(li);
                    });

                    dropdown.classList.add('show');
                });
        });
    </script>

    <script>
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('sparepart-input')) return;

            const input = e.target;
            const dropdown = input.closest('.position-relative')
                .querySelector('.sparepart-dropdown');
            const keyword = input.value.trim();

            if (keyword.length < 2) {
                dropdown.classList.remove('show');
                return;
            }

            fetch(`{{ route('perbaikan-it.sparepart.autocomplete') }}?q=${keyword}`)
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = '';

                    if (!data.length) {
                        dropdown.classList.remove('show');
                        return;
                    }

                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                    <a class="dropdown-item" href="#">${item.name}</a>
                `;

                        li.onclick = (e) => {
                            e.preventDefault();

                            input.value = item.name;
                            input.closest('.position-relative')
                                .querySelector('.sparepart-id').value = item.id;

                            dropdown.classList.remove('show');
                        };

                        dropdown.appendChild(li);
                    });

                    dropdown.classList.add('show');
                })
                .catch(err => console.error(err));
        });
    </script>

    <script>
        // 🔥 HAPUS ROW (dynamic)
        document.getElementById('sparepart-wrapper').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-sparepart')) {
                e.target.closest('.sparepart-row').remove();
            }
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
