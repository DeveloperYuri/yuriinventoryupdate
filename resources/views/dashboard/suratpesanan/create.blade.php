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
                                                    <th>Spare Part</th>
                                                    <th>Qty</th>
                                                    <th>Stok</th> <!-- kolom stok baru -->
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
                                                                <input type="number" name="demand[]" class="form-control"
                                                                    min="1" value="{{ old('demand')[$i] ?? 1 }}">
                                                                @error('demand.' . $i)
                                                                    <div class="text-danger small">{{ $message }}</div>
                                                                @enderror
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
                    $.getJSON('/spareparts/search', {
                        q: request.term
                    }, function(data) {
                        response(data);
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    // Set hidden input dengan id product
                    $(this).siblings('input[name="product[]"]').val(ui.item.id);
                    // Set input text dengan label yg dipilih
                    $(this).val(ui.item.label);

                    const tr = $(this).closest('tr');
                    const stockInput = tr.find('input[name="stock[]"]');

                    // AJAX ambil stok spare part
                    $.getJSON('/spareparts/' + ui.item.id + '/stock', function(data) {
                        stockInput.val(data.stock); // isi stok
                    });

                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>").append("<div>" + item.label + "</div>").appendTo(ul);
            };
        }

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
                <input type="number" name="demand[]" class="form-control" min="1" value="1">
            </td>
            <td>
                <input type="text" name="stock[]" class="form-control" readonly value="0">
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
@endpush
