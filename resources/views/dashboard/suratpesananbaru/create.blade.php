@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-4">Form Pesanan Barang</h2>

                            <form id="myForm" class="mt-4" action="{{ route('suratpesananbaru.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <!-- Kiri -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="no_surat_pesanan"
                                                    value="{{ $noDokumen }}" readonly>
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
                                            <label class="col-sm-4 col-form-label">Department</label>
                                            <div class="col-sm-8">
                                                <select id="department_id" name="department_id"
                                                    class="form-control @error('department_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Department --</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}"
                                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('department_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    </div>

                                    <!-- Kanan -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" name="created_at"
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
                                            <label class="col-sm-4 col-form-label">Sub Category</label>
                                            <div class="col-sm-8">
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

                                <!-- Tab -->
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="operations" role="tabpanel">
                                        <table class="table" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <th>Qty_Minta</th>
                                                    <th>Stok</th> <!-- kolom stok baru -->
                                                    <th>Qty_Kurang</th>
                                                    <th>Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="productTableBody">

                                                <tr>
                                                    <td>
                                                        <input type="text" name="product_name[]"
                                                            class="form-control product-name">

                                                        <input type="hidden" name="item_id[]" class="item-id">
                                                        <input type="hidden" name="item_type[]" class="item-type">
                                                    </td>

                                                    <td>
                                                        <input type="number" name="demand[]" class="form-control qty"
                                                            value="1">
                                                    </td>

                                                    <td>
                                                        <input type="number" name="stock[]" class="form-control stok"
                                                            readonly value="0">
                                                    </td>

                                                    <td>
                                                        <input type="number" name="qty_kurang[]"
                                                            class="form-control qty-kurang" readonly value="0">
                                                    </td>

                                                    <td>
                                                        <input type="text" name="keterangan[]"
                                                            class="form-control keterangan">
                                                    </td>

                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm removeLine">Remove</button>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4" class="text-left">
                                                        <a href="#" id="addLineBtn">Tambah Barang</a>
                                                    </td>
                                                </tr>

                                            </tbody>

                                            {{-- <tbody id="productTableBody">
                                                @if (old('product'))
                                                    @foreach (old('product') as $i => $productId)
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="product_name[]"
                                                                    class="form-control"
                                                                    value="{{ old('product_name')[$i] ?? '' }}"
                                                                    placeholder="Nama Barang">
                                                                <input type="hidden" name="product[]"
                                                                    value="{{ $productId }}">
                                                                @error('product.' . $i)
                                                                    <div class="text-danger small">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="number" name="demand[]"
                                                                    class="form-control qty" min="1"
                                                                    value="{{ old('demand')[$i] ?? 1 }}">

                                                                @error('demand.' . $i)
                                                                    <div class="text-danger small">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
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
                                                            </td>

                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeLine">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif

                                                <tr>
                                                    <td colspan="4" class="text-left">
                                                        <a href="#" id="addLineBtn">Tambah Barang</a>
                                                    </td>
                                                </tr>
                                            </tbody> --}}
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
                                    <a href="{{ route('suratpesananbaru.index') }}" class="btn btn-secondary">Cancel</a>
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
        // 1️⃣ TARUH DI SINI (PALING ATAS)
        function hitungQtyKurang(tr) {
            const stok = parseInt(tr.find('.stok').val()) || 0;
            const minta = parseInt(tr.find('.qty').val()) || 0;

            let kurang = minta - stok;
            if (kurang < 0) kurang = 0;

            tr.find('.qty-kurang').val(kurang);

            // if (kurang > 0) {
            //     tr.find('.keterangan').val('Stok kurang');
            // } else {
            //     tr.find('.keterangan').val('');
            // }
        }
    </script>

    <script>
        function applyAutocomplete(el) {
            el.autocomplete({
                source: function(request, response) {
                    $.getJSON('/autocomplete/items', {
                        q: request.term
                    }, response);
                },
                minLength: 1,
                select: function(event, ui) {
                    const tr = $(this).closest('tr');

                    // nama
                    $(this).val(ui.item.value);

                    // id
                    tr.find('.item-id').val(ui.item.id);

                    // type (INI YANG KURANG)
                    tr.find('.item-type').val(ui.item.item_type);

                    // stok
                    tr.find('.stok').val(ui.item.stock);

                    // hitung qty kurang
                    hitungQtyKurang(tr);

                    return false;
                }

            });
        }
    </script>

    <script>
        $(document).ready(function() {
            // APPLY AUTOCOMPLETE UNTUK ROW PERTAMA
            applyAutocomplete($('.product-name'));
        });
    </script>


    <script>
        $(document).on('click', '#addLineBtn', function(e) {
            e.preventDefault();

            const newRow = `
<tr>
    <td>
    <input type="text" name="product_name[]" class="form-control product-name">
    <input type="hidden" name="item_id[]" class="item-id">
    <input type="hidden" name="item_type[]" class="item-type">
</td>

    <td>
        <input type="number" name="demand[]" class="form-control qty" value="1">
    </td>
    <td>
        <input type="number" name="stock[]" class="form-control stok" readonly value="0">
    </td>
    <td>
        <input type="number" name="qty_kurang[]" class="form-control qty-kurang" readonly value="0">
    </td>
    <td>
        <input type="text" name="keterangan[]" class="form-control keterangan">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm removeLine">Remove</button>
    </td>
</tr>
`;


            const $row = $(newRow);
            $('#addLineBtn').closest('tr').before($row);

            // ⬅️ INI YANG PENTING
            applyAutocomplete($row.find('.product-name'));
        });
    </script>

    <script>
        $(document).on('input', '.qty', function() {
            const tr = $(this).closest('tr');
            hitungQtyKurang(tr);
        });
    </script>

    <script>
        document.getElementById('saveBtn').addEventListener('click', function() {
            this.disabled = true;
            document.getElementById('btnText').innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Saving...';
            this.form.submit();
        });
    </script>

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
        $(document).on('click', '.removeLine', function() {
            $(this).closest('tr').remove();
        });
    </script>
@endpush
