@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 0 || Auth::user()->is_role == 1)
                <a href="{{ route('suratpesanan.create') }}" class="btn btn-primary" dusk="addsparepart">Buat Surat
                    Pesanan</a>

                <div class="d-flex gap-2 align-items-center">
                    @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 0 || Auth::user()->is_role == 1)
                        <button id="btnRekapPdf" class="btn btn-danger d-none" onclick="generateBatchPDF()">
                            <i class="bi bi-file-pdf"></i> Rekap Jadi PDF
                        </button>

                        <button class="btn btn-outline-secondary d-none" id="btnClear" onclick="clearSelections()">
                            Batal Pilih Semua
                        </button>
                    @endif
                </div>

                {{-- <button id="btnRekapPdf" class="btn btn-danger d-none" onclick="generateBatchPDF()">
                    <i class="bi bi-file-pdf"></i> Rekap Jadi PDF
                </button>
                <button class="btn btn-outline-secondary btn-sm d-none" id="btnClear" onclick="clearSelections()">
                    Batal Pilih Semua
                </button> --}}
            @endif

        </div>

        <div class="mt-4">
            <form method="get">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <input id="searchingtitle" type="text" class="form-control"
                            value="{{ Request()->no_surat_pesanan }}" placeholder="Cari Nomer Surat Pesanan"
                            name="no_surat_pesanan">
                    </div>

                    <div class="col-md-4">
                        <select name="ditujukan_kepada" class="form-control">
                            <option value="">-- Di Tujukan Kepada --</option>
                            <option value="JF" {{ request('ditujukan_kepada') == 'JF' ? 'selected' : '' }}>Ko Jefri (JF)
                            </option>
                            <option value="WD" {{ request('ditujukan_kepada') == 'WD' ? 'selected' : '' }}>Bu Widy (WD)
                            </option>
                            <option value="NR" {{ request('ditujukan_kepada') == 'NR' ? 'selected' : '' }}>Bu Nur (NR)
                            </option>
                            <option value="SA" {{ request('ditujukan_kepada') == 'SA' ? 'selected' : '' }}>Sumber Alam
                                (SA)</option>
                            <option value="LN" {{ request('ditujukan_kepada') == 'LN' ? 'selected' : '' }}>Lainnya (LN)
                            </option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Cari</button>
                        <a href="{{ route('suratpesanan.index') }}" class="btn btn-secondary">Reset</a>
                    </div>

                    {{-- <div class="col-auto">
                        <button type="submit" class="btn btn-dark">Cari</button>
                    </div> --}}
                </div>
            </form>
        </div>

        <section class="section">
            <div class="row mt-4">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Daftar Surat Pesanan</h5>

                                @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                    <div class="d-flex gap-2">

                                        <!-- Modal Import -->
                                        <div class="modal fade" id="importModal" tabindex="-1"
                                            aria-labelledby="importModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="importModalLabel">Import Spare Part dari
                                                            Excel</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <form action="{{ route('spare-parts.import') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="file" class="form-label">Pilih File
                                                                    Excel</label>
                                                                <input type="file" name="file" class="form-control"
                                                                    accept=".xlsx,.xls,.csv" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary"
                                                                id="btnImport">Import</button>
                                                        </div>
                                                        {{-- <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Import</button>
                                                        </div> --}}
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if (session('success'))
                                <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: '{{ session('success') }}',
                                        timer: 2000, // 2000 ms = 2 detik
                                        showConfirmButton: false
                                    });
                                </script>
                            @endif

                            <!-- Default Table -->
                            <div class="table-responsive">

                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            {{-- <th class="text-center">No</th> --}}
                                            <th class="text-center">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th class="text-center">No. SP</th>
                                            <th class="text-center">Di Buat Oleh</th>
                                            <th class="text-center">Lokasi</th>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Sub Category</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Status</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($getRecord as $index => $sp)
                                            <tr onclick="window.location='{{ route('suratpesanan.show', $sp->id) }}'"
                                                style="cursor:pointer;">
                                                <td class="text-center" onclick="event.stopPropagation();">
                                                    <input type="checkbox" value="{{ $sp->id }}"
                                                        class="form-check-input item-checkbox"
                                                        onclick="handleSingleCheck(this)">
                                                </td>

                                                <td class="text-center">
                                                    {{ !empty($sp->no_surat_pesanan) ? $sp->no_surat_pesanan : '000-000-000' }}
                                                </td>

                                                <td class="text-center">{{ $sp->name }}</td>
                                                <td class="text-center">{{ $sp->location->name ?? '-' }}</td>
                                                <td class="text-center">{{ $sp->category->name ?? '-' }}</td>
                                                <td class="text-center">{{ $sp->subcategory->name ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($sp->tanggal)->format('d-m-Y') }}</td>

                                                <td class="text-center">
                                                    @if ($sp->status == 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif ($sp->status == 'onprogress')
                                                        <span class="badge bg-warning">On Progress</span>
                                                    @elseif ($sp->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif ($sp->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>

                                                <td class="text-nowrap">
                                                    @if ($sp->status == 'draft')
                                                        <a href="{{ route('suratpesanan.edit', $sp->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>

                                                        <form action="{{ route('suratpesanan.delete', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="event.stopPropagation(); confirmDelete(this.form)">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan.submit', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-warning">Ajukan</button>
                                                        </form>
                                                        {{-- @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 1 || auth()->user()->is_role == 2)) --}}
                                                    @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 2 || auth()->user()->name == 'widy'))
                                                        <form action="{{ route('suratpesanan.approve', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        <form action="{{ route('suratpesanan.reject', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- End Default Table Example -->
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

                            <!-- PAGINATION LINK -->
                            <div class="d-flex justify-content-center">
                                {{ $getRecord->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main><!-- End #main -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const importForm = document.querySelector('#importModal form');
            const btnImport = document.getElementById('btnImport');

            importForm.addEventListener('submit', function() {
                btnImport.disabled = true;
                btnImport.innerHTML = 'Importing...';
            });
        });
    </script>

    <script>
        $(function() {
            $("#searchingtitle").autocomplete({
                source: "{{ route('spare-parts.autocomplete') }}",
                minLength: 2, // mulai search setelah 2 karakter
            });
        });
    </script>

    <script>
        // Nama kunci penyimpanan di browser
        const STORAGE_KEY = 'selected_surat_pesanan';

        document.addEventListener('DOMContentLoaded', function() {
            renderCheckboxesFromStorage();
            toggleBatchButton();

            // Event Select All (Hanya berlaku untuk halaman yang aktif)
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                let selectedIds = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    if (this.checked) {
                        if (!selectedIds.includes(cb.value)) selectedIds.push(cb.value);
                    } else {
                        selectedIds = selectedIds.filter(id => id !== cb.value);
                    }
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedIds));
                toggleBatchButton();
            });
        });

        // Fungsi saat checkbox satuan diklik
        function handleSingleCheck(element) {
            let selectedIds = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
            const id = element.value;

            if (element.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
                document.getElementById('selectAll').checked = false; // Uncheck select all
            }

            localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedIds));
            toggleBatchButton();
        }

        // Fungsi untuk mencentang ulang checkbox saat pindah halaman
        function renderCheckboxesFromStorage() {
            const selectedIds = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
            const checkboxes = document.querySelectorAll('.item-checkbox');

            checkboxes.forEach(cb => {
                if (selectedIds.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }

        // Fungsi kontrol tampilan tombol Rekap & Batal Pilih
        function toggleBatchButton() {
            const selectedIds = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
            const btnRekapPdf = document.getElementById('btnRekapPdf');
            const btnClear = document.getElementById('btnClear'); // Ambil element tombol Batal

            if (selectedIds.length > 0) {
                // Tampilkan tombol Rekap
                btnRekapPdf.classList.remove('d-none');
                btnRekapPdf.innerHTML = `<i class="bi bi-file-pdf"></i> Rekap ${selectedIds.length} Surat ke PDF`;

                // Tampilkan tombol Batal Pilih
                btnClear.classList.remove('d-none');
            } else {
                // Sembunyikan kedua tombol jika tidak ada yang dipilih
                btnRekapPdf.classList.add('d-none');
                btnClear.classList.add('d-none');
            }
        }

        // Fungsi Kirim ke PDF
        function generateBatchPDF() {
            const selectedIds = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

            if (selectedIds.length > 0) {
                // Gabungkan ID jadi string: 1,2,3
                const idsString = selectedIds.join(',');

                // Arahkan ke route cetak (Ganti dengan route PDF-mu) window.location.href = "{{ url('suratpesanan/rekap-pdf') }}?ids=" + idsString;

                window.open("{{ url('suratpesanan/rekap-pdf') }}?ids=" + idsString, '_blank');

                // OPSIONAL: Bersihkan storage setelah cetak
                // localStorage.removeItem(STORAGE_KEY);
            }
        }

        function clearSelections() {
            localStorage.removeItem(STORAGE_KEY);
            location.reload(); // Refresh halaman untuk uncheck semua
        }
    </script>
@endpush

@if ($errors->any())
    @push('scripts')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal import!',
                text: '{{ $errors->first() }}'
            });
        </script>
    @endpush
@endif
