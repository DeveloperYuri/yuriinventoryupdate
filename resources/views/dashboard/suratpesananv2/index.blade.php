@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 0 || Auth::user()->is_role == 1)
                <a href="{{ route('v2suratpesanan.create') }}" class="btn btn-primary" dusk="addsparepart">Buat Surat
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

        {{-- <div class="pagetitle d-flex justify-content-between align-items-center">
            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 0 || Auth::user()->is_role == 1)
                <a href="{{ route('v2suratpesanan.create') }}" class="btn btn-primary" dusk="addsparepart">Buat Surat
                    Pesanan Baru V2</a>
            @endif

        </div> --}}

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
                        <a href="{{ route('v2suratpesanan.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
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
                                            <th class="text-center">Status Penerimaan</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Status</th>

                                            @if (Auth::user()->is_role == 2 || Auth::user()->is_role == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($getRecord as $index => $sp)
                                            <tr onclick="window.location='{{ route('v2suratpesanan.show', $sp->id) }}'"
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
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        @php
                                                            $statusPenerimaan = $sp->status_penerimaan ?? 'pending';
                                                            $dotColor =
                                                                [
                                                                    'open' => '#6c757d', // Abu-abu
                                                                    'proses' => '#ffc107', // kuning
                                                                    'terima sebagian' => '#ffc107', // kuning
                                                                    'closed' => '#198754', // Hijau
                                                                    'cancel' => '#dc3545', // Merah
                                                                ][$statusPenerimaan] ?? '#6c757d';
                                                        @endphp

                                                        <span
                                                            style="height: 10px; width: 10px; background-color: {{ $dotColor }}; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>

                                                        <span
                                                            style="font-weight: 600; color: {{ $dotColor }}; font-size: 0.9rem;">
                                                            {{ ucfirst($statusPenerimaan) }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="text-center">{{ $sp->keterangan ?? '-' }}</td>

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
                                                        <a href="{{ route('v2suratpesanan.edit', $sp->id) }}"
                                                            class="btn btn-sm btn-primary">Edit</a>

                                                        <form action="{{ route('v2suratpesanan.delete', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="event.stopPropagation(); confirmDelete(this.form)">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('v2suratpesanan.submit', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-warning">Ajukan</button>
                                                        </form>
                                                        {{-- @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 1 || auth()->user()->is_role == 2)) --}}
                                                    @elseif ($sp->status == 'onprogress' && (auth()->user()->is_role == 2 || auth()->user()->name == 'widy'))
                                                        <form action="{{ route('v2suratpesanan.approve', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Approve</button>
                                                        </form>

                                                        {{-- Ganti form reject yang lama dengan ini --}}
                                                        <form id="reject-form-{{ $sp->id }}"
                                                            action="{{ route('v2suratpesanan.reject', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="alasan_reject"
                                                                id="input-reject-{{ $sp->id }}">
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="event.stopPropagation(); showRejectModal({{ $sp->id }})">
                                                                Reject
                                                            </button>
                                                        </form>

                                                        {{-- <form action="{{ route('v2suratpesanan.reject', $sp->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Reject</button>
                                                        </form> --}}
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
        function showRejectModal(id) {
            Swal.fire({
                title: 'Alasan Penolakan',
                input: 'textarea',
                inputLabel: 'Silahkan masukkan alasan mengapa SP ini ditolak',
                inputPlaceholder: 'Ketik alasan di sini...',
                inputAttributes: {
                    'aria-label': 'Ketik alasan di sini'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Reject!',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan harus diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Isi input hidden dengan alasan dari SweetAlert
                    document.getElementById('input-reject-' + id).value = result.value;
                    // Submit form
                    document.getElementById('reject-form-' + id).submit();
                }
            });
        }
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
