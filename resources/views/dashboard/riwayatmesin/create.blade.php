@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Riwayat Mesin</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('store.riwayatmesin') }}" method="POST">
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Tanggal<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" name="tanggal"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Kategori Mesin</label>
                                    <div class="col-sm-10">
                                        <select id="category_id" name="category_id"
                                            class="form-control @error('category_id') is-invalid @enderror">
                                            <option value="">-- Pilih Kategori Mesin --</option>
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
                                    <label class="col-sm-2 col-form-label">Sub Category</label>
                                    <div class="col-sm-10">
                                        <select id="subcategory_id" name="subcategory_id"
                                            class="form-control @error('subcategory_id') is-invalid @enderror">
                                            <option value="">-- Pilih Sub Kategori Mesin --</option>
                                            {{-- jika ada subcategories awal (misal edit), bisa looping di sini --}}

                                        </select>
                                        @error('subcategory_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Running Hour<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="running_hour"
                                            required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Pekerjaan<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="pekerjaan" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">PIC<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="pic" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Deskripsi<span
                                            style="color: red">*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="deskripsi"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Status<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select class="form-select" name="status" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Proses">Proses</option>
                                            <option value="Rusak">Rusak</option>
                                            <option value="Selesai">Selesai</option>
                                        </select>
                                    </div>
                                </div>


                                {{-- <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Status<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="status" required>
                                    </div>
                                </div> --}}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('index.riwayatmesin') }}" class="btn btn-secondary">kembali</a>
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
        $(document).ready(function() {
            console.log("JQUERY READY JALAN");
            
            var oldSubcategoryId = '{{ old('subcategory_id') }}';

            $('#category_id').on('change', function() {
                var categoryId = $(this).val();
                $('#subcategory_id').html('<option value="">-- Pilih Sub Kategori Mesin --</option>');

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

            // Trigger otomatis jika ada old value
            var oldCategoryId = '{{ old('category_id') }}';
            if (oldCategoryId) {
                $('#category_id').val(oldCategoryId).trigger('change');
            }
        });
    </script>
@endpush
