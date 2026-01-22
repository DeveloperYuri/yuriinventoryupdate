@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Spare Part IT</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('sparepart-it.store') }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">IMAGE<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            name="image">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Nama<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="inputText" name="name" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select name="satuan_id"
                                            class="form-control @error('satuan_id') is-invalid @enderror">
                                            <option value="">-- Pilih Satuan --</option>
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ old('satuan_id') == $s->id ? 'selected' : '' }}>
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('satuan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="{{ route('sparepart-it.index') }}" class="btn btn-secondary">Back</a>
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
        document.getElementById("myForm").addEventListener("keydown", function(event) {
            if (event.key === "Enter" && event.target.tagName !== "TEXTAREA") {
                event.preventDefault();
            }
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
@endpush
