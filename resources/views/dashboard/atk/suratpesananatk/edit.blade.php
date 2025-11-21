@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-4">Form Pemesanan Barang ATK</h2>

                            <form id="myForm" class="mt-4"
                                action="{{ route('suratpesanan-atk.update', $transaction->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <!-- Kiri -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="no_surat_pesanan"
                                                    value="{{ $transaction->no_surat_pesanan }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di buat oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('dibuat_oleh') is-invalid @enderror"
                                                    name="dibuat_oleh" value="{{ $transaction->dibuat_oleh }}">
                                                @error('dibuat_oleh')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kanan -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-3 col-form-label">Tanggal</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="created_at"
                                                    value="{{ now()->format('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-3 col-form-label">Lokasi</label>
                                            <div class="col-sm-9">
                                                <select name="locations_id"
                                                    class="form-control @error('locations_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Lokasi --</option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}"
                                                            {{ old('locations_id', $transaction->locations_id) == $location->id ? 'selected' : '' }}>
                                                            {{ $location->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('locations_id')
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
                                                    <th>Nama ATK</th>
                                                    <th>Qty</th>
                                                    <th>Stok</th> <!-- kolom stok baru -->
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="productTableBody">

                                                {{-- Jika ada OLD() (validasi gagal), pakai OLD dulu --}}
                                                @if (old('product'))
                                                    @foreach ($transaction->details as $index => $detail)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $detail->atk->name }}" placeholder="Nama ATK"
                                                                    readonly>

                                                                <input type="hidden"
                                                                    name="details[{{ $index }}][id]"
                                                                    value="{{ $detail->id }}">
                                                                <input type="hidden"
                                                                    name="details[{{ $index }}][atk_id]"
                                                                    value="{{ $detail->atk_id }}">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="details[{{ $index }}][qty]"
                                                                    class="form-control" value="{{ $detail->qty }}"
                                                                    min="1">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="details[{{ $index }}][stock]"
                                                                    class="form-control" value="{{ $detail->stock }}"
                                                                    readonly>
                                                            </td>

                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeLine">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    {{-- @foreach (old('product') as $i => $productId)
                                                        <tr>
                                                            <td>
                                                                <select name="details[{{ $index }}][atk_id]"
                                                                    class="form-control">
                                                                    @foreach ($spareparts as $sp)
                                                                        <option value="{{ $sp->id }}"
                                                                            {{ $sp->id == $item->atk_id ? 'selected' : '' }}>
                                                                            {{ $sp->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>


                                                            <td>
                                                                <input type="number" name="demand[]" class="form-control"
                                                                    min="1" value="{{ old('demand')[$i] ?? 1 }}">
                                                            </td>

                                                            <td>
                                                                <input type="text" name="stock[]" class="form-control"
                                                                    readonly value="{{ old('stock')[$i] ?? '0' }}">
                                                            </td>

                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeLine">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach --}}

                                                    {{-- Jika tidak ada OLD(), tampilkan DATA EDIT --}}
                                                @elseif(isset($transaction))
                                                    @foreach ($transaction->details as $index => $detail)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $detail->atk->name ?? '' }}"
                                                                    placeholder="Nama ATK" readonly>

                                                                <input type="hidden"
                                                                    name="details[{{ $index }}][id]"
                                                                    value="{{ $detail->id }}">
                                                                <input type="hidden"
                                                                    name="details[{{ $index }}][atk_id]"
                                                                    value="{{ $detail->atk_id }}">
                                                            </td>

                                                            <td>
                                                                <input type="number"
                                                                    name="details[{{ $index }}][qty]"
                                                                    class="form-control" min="1"
                                                                    value="{{ $detail->qty }}">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="details[{{ $index }}][stock]"
                                                                    class="form-control" readonly
                                                                    value="{{ $detail->stock }}">
                                                            </td>

                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeLine">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif


                                                {{-- Tombol tambah ATK --}}
                                                <tr>
                                                    <td colspan="4" class="text-left">
                                                        <a href="#" id="addLineBtn">Tambah ATK</a>
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
                                    <a href="{{ route('suratpesanan-atk.index') }}" class="btn btn-secondary">Cancel</a>
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
        $(document).ready(function() {

            let table = $('#productTable');
            let rowIndex = {{ $transaction->details->count() ?? 0 }};
            const addLineBtn = $('#addLineBtn');

            // INIT AUTOCOMPLETE UNTUK BARIS EXISTING
            table.find('.product-autocomplete').each(function() {
                applyAutocomplete($(this));
            });

            // ===========================
            //  AUTOCOMPLETE FUNCTION
            // ===========================
            function applyAutocomplete(el) {
                el.autocomplete({
                    source: function(request, response) {
                        $.getJSON('/atk/search', {
                            q: request.term
                        }, function(data) {
                            response(data);
                        });
                    },
                    minLength: 1,
                    select: function(event, ui) {

                        // Isi hidden atk_id
                        $(this).closest('td')
                            .find('input[name*="[atk_id]"]')
                            .val(ui.item.id);

                        // Tampilkan label
                        $(this).val(ui.item.label);

                        // Isi stok
                        const tr = $(this).closest('tr');
                        const stockInput = tr.find('input[name*="[stock]"]');

                        $.getJSON('/atk/' + ui.item.id + '/stock', function(data) {
                            stockInput.val(data.stock);
                        });

                        return false;
                    }
                }).autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.label + "</div>")
                        .appendTo(ul);
                };
            }

            // ===========================
            //  ADD NEW ROW
            // ===========================
            addLineBtn.on('click', function(event) {
                event.preventDefault();

                const newRow = $(`
<tr>
    <td>
        <input type="text" 
               class="form-control product-autocomplete" 
               name="details[` + rowIndex + `][product_display]"
               placeholder="Masukkan Nama ATK">

        <input type="hidden" name="details[` + rowIndex + `][id]">
        <input type="hidden" name="details[` + rowIndex + `][atk_id]">
    </td>

    <td>
        <input type="number" name="details[` + rowIndex + `][qty]"
               class="form-control" min="1" value="1">
    </td>

    <td>
        <input type="text" name="details[` + rowIndex + `][stock]"
               class="form-control" readonly value="0">
    </td>

    <td>
        <button type="button" class="btn btn-danger btn-sm removeLine">Remove</button>
    </td>
</tr>
        `);

                newRow.insertBefore(addLineBtn.closest('tr'));

                // Pasang autocomplete untuk baris baru
                applyAutocomplete(newRow.find('.product-autocomplete'));

                rowIndex++;
            });

            // ===========================
            //  REMOVE ROW
            // ===========================
            table.on('click', '.removeLine', function() {
                $(this).closest('tr').remove();
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
