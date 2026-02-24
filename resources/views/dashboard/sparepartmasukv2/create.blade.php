@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-4">Form Penerimaan Barang</h2>

                            <form id="myForm" class="mt-4" action="{{ route('sparepartinmultiple.store') }}"
                                method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <!-- Kiri -->
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No Dokumen</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="no_dokumen"
                                                    value="{{ $noDokumen }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di terima dari</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control @error('diterima_dari') is-invalid @enderror"
                                                    name="diterima_dari" value="{{ old('diterima_dari') }}">
                                                @error('diterima_dari')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Supplier</label>
                                            <div class="col-sm-8">
                                                <select name="supplier_id" class="form-control">
                                                    <option value="">-- Pilih Supplier --</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- @error('supplier_id')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror --}}
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Kanan -->
                                    <div class="col-md-6">

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Tanggal</label>
                                            <div class="col-sm-8">
                                                <input id="tanggalMulai" name="tanggal" type="text" class="form-control"
                                                    placeholder="Pilih tanggal..." autocomplete="off"
                                                    value="{{ now()->format('Y-m-d') }}">
                                                <input type="hidden" name="tanggal" id="tanggalHidden"
                                                    value="{{ now()->format('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di terima oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text"
                                                    class="form-control  @error('diterima_oleh') is-invalid @enderror"
                                                    name="diterima_oleh" value="{{ old('diterima_oleh') }}">
                                                @error('diterima_oleh')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No. SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="po_autocomplete"
                                                    name="po_numbers" value="{{ old('po_numbers') }}"
                                                    placeholder="Cari No SP">

                                                <input type="hidden" name="po_id" id="po_id">
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
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
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
                                    <a href="{{ route('v2sparepartinmultiple.index') }}"
                                        class="btn btn-secondary">Cancel</a>
                                </div>

                                {{-- <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('sparepartinmultiple.index') }}" class="btn btn-secondary">Cancel</a>
                                </div> --}}
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
                        return false; // mencegah nilai autocomplete default ditimpa
                    }
                })
                .autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.label + "</div>")
                        .appendTo(ul);
                };
        }


        $(function() {
            const table = $('#productTable tbody');
            const addLineBtn = $('#addLineBtn');

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
                        <button type="button" class="btn btn-danger btn-sm removeLine">Remove</button>
                    </td>
                </tr>
            `);
                table.find('tr:last').before(newRow);
                applyAutocomplete(newRow.find('input[name="product_name[]"]'));

                newRow.find('.removeLine').on('click', function() {
                    newRow.remove();
                });
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
        document.getElementById("myForm").addEventListener("keydown", function(event) {
            if (event.key === "Enter" && event.target.tagName !== "TEXTAREA") {
                event.preventDefault();
            }
        });
    </script>

    <script>
        $(function() {
            $('#po_autocomplete').autocomplete({
                source: function(request, response) {
                    $.getJSON("{{ route('suratpesanan.search') }}", {
                        q: request.term
                    }, response);
                },
                minLength: 1,
                select: function(event, ui) {
                    $('#po_autocomplete').val(ui.item.label);
                    $('#po_id').val(ui.item.id);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };
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
    </script>
@endpush
