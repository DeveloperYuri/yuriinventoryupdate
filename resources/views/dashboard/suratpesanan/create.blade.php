@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-4">Form Pemesanan Barang</h2>

                            <form id="myForm" class="mt-4" action="{{ route('suratpesanan.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <!-- Kiri -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="no_surat_pesanan"
                                                    name="no_surat_pesanan" value="{{ $noDokumen }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di buat oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Category</label>
                                            <div class="col-sm-8">
                                                <select id="category_id" name="category_id"
                                                    class="form-control @error('category_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Category --</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Ditujukan kepada</label>
                                            <div class="col-sm-8">
                                                <select id="ditujukan_kepada" name="ditujukan_kepada"
                                                    class="form-control @error('ditujukan_kepada') is-invalid @enderror">
                                                    <option value="">-- Silahkan Pilih --</option>
                                                    <option value="JF"
                                                        {{ old('ditujukan_kepada') == 'JF' ? 'selected' : '' }}>Ko Jefri
                                                    </option>
                                                    <option value="WD"
                                                        {{ old('ditujukan_kepada') == 'WD' ? 'selected' : '' }}>Bu Widy
                                                    </option>
                                                    <option value="NR"
                                                        {{ old('ditujukan_kepada') == 'NR' ? 'selected' : '' }}>Bu Nur
                                                    </option>
                                                    <option value="SA"
                                                        {{ old('ditujukan_kepada') == 'SA' ? 'selected' : '' }}>Sumber Alam
                                                    </option>
                                                    <option value="LN"
                                                        {{ old('ditujukan_kepada') == 'LN' ? 'selected' : '' }}>Lainnya
                                                    </option>
                                                </select>
                                                @error('ditujukan_kepada')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Ditujukan kepada</label>
                                            <div class="col-sm-8">
                                                <select id="category_id" name="category_id"
                                                    class="form-control @error('category_id') is-invalid @enderror">
                                                    <option value="">-- Silahkan Pilih --</option>

                                                    <option value="1"
                                                        {{ old('category_id') == '1' ? 'selected' : '' }}>Ko Jefri
                                                    </option>
                                                    <option value="2"
                                                        {{ old('category_id') == '2' ? 'selected' : '' }}>Bu Widy
                                                    </option>
                                                    <option value="3"
                                                        {{ old('category_id') == '3' ? 'selected' : '' }}>Bu Nur
                                                    </option>
                                                    <option value="3"
                                                        {{ old('category_id') == '3' ? 'selected' : '' }}>Sumber Alam
                                                    </option>

                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                    </div>

                                    <!-- Kanan -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" name="tanggal"
                                                    value="{{ now()->format('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Lokasi</label>
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
                                            <label class="col-sm-2 col-form-label">Sub Category</label>
                                            <div class="col-sm-10">
                                                <select id="subcategory_id" name="subcategory_id"
                                                    class="form-control @error('subcategory_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Sub Category --</option>
                                                    {{-- jika ada subcategories awal (misal edit), bisa looping di sini --}}

                                                </select>
                                                @error('subcategory_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div id="validationAlert" class="alert alert-danger alert-dismissible fade d-none"
                                    role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>
                                    <strong>Pemesanan Ditolak!</strong>
                                    <div id="alertMessage" class="mt-2" style="white-space: pre-line;"></div>
                                    <button type="button" class="btn-close"
                                        onclick="$(this).closest('.alert').addClass('d-none').removeClass('show')"></button>
                                </div>

                                <!-- Tab -->
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Spare Part</th>
                                                    <th>Qty_Minta</th>
                                                    <th>Stok</th> <!-- kolom stok baru -->
                                                    <th>Qty_Kurang</th>
                                                    <th>Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="productTableBody">
                                                @if (old('product'))
                                                    @foreach (old('product') as $i => $productId)
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="product_name[]"
                                                                    class="form-control"
                                                                    value="{{ old('product_name')[$i] ?? '' }}"
                                                                    placeholder="Nama Spare Part">
                                                                <input type="hidden" name="product[]"
                                                                    value="{{ $productId }}">
                                                                @error('product.' . $i)
                                                                    <div class="text-danger small">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="number" name="demand[]"
                                                                    class="form-control qty-minta" min="1"
                                                                    value="{{ old('demand')[$i] ?? 1 }}">

                                                                @error('demand.' . $i)
                                                                    <div class="text-danger small">{{ $message }}</div>
                                                                @enderror
                                                            </td>

                                                            <td>
                                                                <input type="number" name="stock[]"
                                                                    class="form-control stok" readonly
                                                                    value="{{ old('stock')[$i] ?? 0 }}">
                                                            </td>

                                                            <td>
                                                                <input type="number" name="qty_kurang[]"
                                                                    class="form-control qty-kurang" readonly
                                                                    value="{{ old('qty_kurang')[$i] ?? 0 }}">
                                                            </td>

                                                            <td>
                                                                <input type="text" name="keterangan[]"
                                                                    class="form-control keterangan"
                                                                    value="{{ old('keterangan')[$i] ?? '' }}">
                                                            </td>
                                                            {{-- <td>
                                                                <input type="number" name="stock[]"
                                                                    class="form-control stok" readonly value="0">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="qty_kurang[]"
                                                                    class="form-control qty-kurang" readonly
                                                                    value="0">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="keterangan[]"
                                                                    class="form-control keterangan" readonly
                                                                    value="">
                                                            </td> --}}

                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeLine">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif

                                                <tr>
                                                    <td colspan="4" class="text-left">
                                                        <a href="#" id="addLineBtn">Add Spare Part</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="tab-pane fade" id="additional" role="tabpanel">
                                        <textarea class="form-control" rows="4" placeholder="Additional Information"></textarea>
                                    </div>
                                    <div class="tab-pane fade" id="note" role="tabpanel">
                                        <textarea class="form-control" rows="4" placeholder="Note"></textarea>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="saveBtn">
                                        <span id="btnText">Save</span>
                                    </button>
                                    <a href="{{ route('suratpesanan.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        function applyAutocomplete(el) {
            el.autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '/spareparts/search',
                        dataType: 'json',
                        data: {
                            q: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    const tr = $(this).closest('tr');

                    // isi nama & id produk
                    $(this).val(ui.item.label);
                    tr.find('input[name="product[]"]').val(ui.item.id);

                    const stockInput = tr.find('.stok');

                    // ambil stok
                    $.getJSON('/spareparts/' + ui.item.id + '/stock', function(data) {
                        stockInput.val(data.stock);
                        hitungQtyKurang(tr);
                    });

                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };
        }

        // function applyAutocomplete(el) {
        //     el.autocomplete({
        //         source: function(request, response) {
        //             $.getJSON('/spareparts/' + ui.item.id + '/stock', function(data) {
        //                 stockInput.val(data.stock);

        //                 // 🔥 WAJIB: hitung ulang qty kurang setelah stok masuk
        //                 hitungQtyKurang(tr);
        //             });

        //             // $.getJSON('/spareparts/search', {
        //             //     q: request.term
        //             // }, function(data) {
        //             //     response(data);
        //             // });
        //         },
        //         minLength: 1,
        //         select: function(event, ui) {
        //             // Set hidden input dengan id product
        //             $(this).siblings('input[name="product[]"]').val(ui.item.id);
        //             // Set input text dengan label yg dipilih
        //             $(this).val(ui.item.label);

        //             const tr = $(this).closest('tr');
        //             const stockInput = tr.find('input[name="stock[]"]');

        //             // AJAX ambil stok spare part
        //             $.getJSON('/spareparts/' + ui.item.id + '/stock', function(data) {
        //                 stockInput.val(data.stock); // isi stok
        //             });

        //             return false;
        //         }
        //     }).autocomplete("instance")._renderItem = function(ul, item) {
        //         return $("<li>").append("<div>" + item.label + "</div>").appendTo(ul);
        //     };
        // }

        $(function() {
            const table = $('#productTableBody');
            const addLineBtn = $('#addLineBtn');

            // Pasang autocomplete ke input yang sudah ada
            table.find('input[name="product_name[]"]').each(function() {
                applyAutocomplete($(this));
            });

            // Remove baris
            table.on('click', '.removeLine', function() {
                $(this).closest('tr').remove();
            });

            // Add Spare Part
            addLineBtn.on('click', function(event) {
                event.preventDefault();
                const newRow = $(`
        <tr>
            <td>
                <input type="text" name="product_name[]" class="form-control" placeholder="Nama Spare Part">
                <input type="hidden" name="product[]">
            </td>
            <td>
                <input type="number" name="demand[]" class="form-control qty-minta" min="1" value="1">
            </td>
            <td>
<input type="number" name="stock[]" class="form-control stok" readonly value="0">
            </td>
            <td>
        <input type="number" name="qty_kurang[]" class="form-control qty-kurang" readonly value="0">
    </td>
    <td>
    <input type="text" name="keterangan[]" class="form-control keterangan" value="">
</td>
            
            <td>
                <button type="button" class="btn btn-danger btn-sm removeLine">Remove</button>
            </td>
        </tr>
        `);
                newRow.insertBefore($('#addLineBtn').closest('tr'));
                applyAutocomplete(newRow.find('input[name="product_name[]"]'));
            });
        });
    </script>

    <script>
        function hitungQtyKurang(tr) {
            const qtyMinta = parseInt(tr.find('.qty-minta').val()) || 0;
            const stok = parseInt(tr.find('.stok').val()) || 0;

            let kurang = qtyMinta - stok;
            if (kurang < 0) kurang = 0;

            tr.find('.qty-kurang').val(kurang);
        }
    </script>

    <script>
        $(document).on('input', '.qty-minta', function() {
            const tr = $(this).closest('tr');
            hitungQtyKurang(tr);
        });
    </script>

    <script>
        document.getElementById('saveBtn').addEventListener('click', function(e) {
            // Mencegah form submit otomatis
            e.preventDefault();

            let adaStokTersedia = false;
            let daftarBarang = [];
            const alertBox = $('#validationAlert');
            const alertMessage = $('#alertMessage');

            // Sembunyikan alert jika sebelumnya sudah muncul
            alertBox.addClass('d-none').removeClass('show');

            // Looping setiap baris di tabel produk
            $('#productTableBody tr').each(function(index, row) {
                const productId = $(row).find('input[name="product[]"]').val();

                // Hanya cek baris yang sudah diisi produknya
                if (productId) {
                    const qtyKurang = parseInt($(row).find('.qty-kurang').val()) || 0;
                    const namaBarang = $(row).find('input[name="product_name[]"]').val() ||
                        "Barang baris " + (index + 1);

                    if (qtyKurang === 0) {
                        adaStokTersedia = true;
                        daftarBarang.push("- " + namaBarang);
                    }
                }
            });

            if (adaStokTersedia) {
                // Tampilkan Alert Bootstrap dengan kata-kata yang sama seperti tadi
                alertMessage.html(
                    "Barang berikut masih memiliki stok yang cukup di gudang:<br>" +
                    daftarBarang.join('<br>') +
                    "<br><br>Silakan hapus barang tersebut atau sesuaikan jumlah permintaan."
                );

                alertBox.removeClass('d-none').addClass('show');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // Pastikan tombol tetap aktif agar bisa diklik lagi setelah user memperbaiki data
                $(this).prop('disabled', false);
                document.getElementById('btnText').innerHTML = 'Save';
            } else {
                // Jika validasi lolos, jalankan proses saving
                $(this).prop('disabled', true);
                document.getElementById('btnText').innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Saving...';
                this.form.submit();
            }
        });
    </script>


    {{-- <script>
        document.getElementById('saveBtn').addEventListener('click', function() {
            this.disabled = true;
            document.getElementById('btnText').innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Saving...';
            this.form.submit();
        });
    </script> --}}

    <script>
        $(document).ready(function() {
            var oldSubcategoryId = '{{ old('subcategory_id') }}';

            $('#category_id').on('change', function() {
                var categoryId = $(this).val();
                $('#subcategory_id').html('<option value="">-- Pilih Sub Category --</option>'); // reset

                if (categoryId) {
                    $.ajax({
                        url: '/get-subcategories/' + categoryId,
                        type: 'GET',
                        success: function(data) {
                            $.each(data, function(key, subcat) {
                                var selected = (oldSubcategoryId == subcat.id) ?
                                    'selected' : '';
                                $('#subcategory_id').append(
                                    '<option value="' + subcat.id + '" ' +
                                    selected + '>' + subcat.name + '</option>'
                                );
                            });
                        }
                    });
                }
            });

            // Trigger change jika ada old('category_id') agar subcategory otomatis terisi
            var oldCategoryId = '{{ old('category_id') }}';
            if (oldCategoryId) {
                $('#category_id').val(oldCategoryId).trigger('change');
            }
        });
    </script>

    <script>
        document.getElementById("myForm").addEventListener("keydown", function(event) {
            if (event.key === "Enter" && event.target.tagName !== "TEXTAREA") {
                event.preventDefault();
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Data ini berisi angka terakhir, contoh: { "JF": 1, "WD": 5 }
            const lastNumbers = @json($lastNumbers);

            console.log('masuk ngga');
            console.log("Data dari Laravel:", lastNumbers);
            const noSPInput = $('#no_surat_pesanan');
            const baseNo = "{{ $noDokumen }}"; // Contoh: SP/II/2026/083

            $('#ditujukan_kepada').on('change', function() {
                let inisial = $(this).val();

                if (inisial) {
                    // Ambil angka terakhir dari database untuk inisial ini
                    // Jika JF sudah ada 1 di DB, maka lastNo = 1
                    let lastNo = (lastNumbers && lastNumbers[inisial]) ? parseInt(lastNumbers[inisial]) : 0;

                    // Tambah 1 agar menjadi urutan berikutnya
                    let nextNo = lastNo + 1;
                    let formattedSuffix = nextNo.toString().padStart(2, '0');

                    // Set hasil: SP/II/2026/083/JF-02
                    noSPInput.val(baseNo + '/' + inisial + '-' + formattedSuffix);
                } else {
                    noSPInput.val(baseNo);
                }
            });
        });
    </script>

    {{-- <script>
        $(document).ready(function() {
            // Simpan nomor asli dari controller sebagai patokan awal
            const noAsli = "{{ $noDokumen }}";
            const noSPInput = $('#no_surat_pesanan');

            $('#ditujukan_kepada').on('change', function() {
                let inisial = $(this).val();

                if (inisial) {
                    // Pecah string SP/II/2026/080 berdasarkan "/"
                    let parts = noAsli.split('/');

                    // parts[0] = SP
                    // parts[1] = II
                    // parts[2] = 2026
                    // parts[3] = 080

                    // Sisipkan inisial sebelum bagian terakhir (nomor urut)
                    // Hasil: SP/II/2026/JF/080
                    let noBaru = parts[0] + '/' + parts[1] + '/' + inisial + '/' + parts[2] + '/' + parts[
                    3];

                    noSPInput.val(noBaru);
                } else {
                    // Jika pilihan dikosongkan kembali, kembalikan ke nomor asli
                    noSPInput.val(noAsli);
                }
            });
        });
    </script> --}}
@endpush
