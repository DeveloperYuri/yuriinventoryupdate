@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Riwyat Mesin</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('update.riwayatmesin', $data->id) }}" method="POST">
                                @method('PUT')
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
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">-- Pilih Category --</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}"
                                                    {{ $data->category_id == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
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
                                        <select name="subcategory_id" id="subcategory_id" class="form-control">
                                            <option value="">-- Pilih SubCategory --</option>
                                            @foreach ($subcategories as $sub)
                                                <option value="{{ $sub->id }}"
                                                    {{ $data->subcategory_id == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
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
                                            value="{{ $data->running_hour }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Pekerjaan<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="pekerjaan"
                                            value="{{ $data->pekerjaan }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">PIC<span
                                            style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="pic"
                                            value="{{ $data->pic }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Deskripsi
                                    </label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="deskripsi">{{ $data->deskripsi }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Status<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <select class="form-select" name="status" required>
                                            <option value="">-- Pilih Status --</option>

                                            <option value="Pending" {{ $data->status == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="Proses" {{ $data->status == 'Proses' ? 'selected' : '' }}>Proses
                                            </option>
                                            <option value="Rusak" {{ $data->status == 'Rusak' ? 'selected' : '' }}>Rusak
                                            </option>
                                            <option value="Selesai" {{ $data->status == 'Selesai' ? 'selected' : '' }}>
                                                Selesai</option>
                                        </select>
                                    </div>
                                </div>



                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('index.riwayatmesin') }}" class="btn btn-secondary">Kembali</a>
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

            console.log("Ajax READY JALAN");

            var initialCategoryId = '{{ old('category_id', $data->category_id) }}';
            var initialSubcategoryId = '{{ old('subcategory_id', $data->subcategory_id) }}';
            var firstLoad = true; // ⬅️ kunci penting

            $('#category_id').on('change', function() {

                var categoryId = $(this).val();
                $('#subcategory_id').html('<option value="">-- Pilih Sub Kategori Mesin --</option>');

                if (categoryId) {

                    $.ajax({
                        url: '/get-subcategories/' + categoryId,
                        type: 'GET',
                        success: function(data) {

                            data.forEach(function(subcat) {

                                // HANYA pakai initialSubcategory sekali saat first load!
                                var selected = '';

                                if (firstLoad && initialSubcategoryId == subcat.id) {
                                    selected = 'selected';
                                }

                                $('#subcategory_id').append(
                                    `<option value="${subcat.id}" ${selected}>${subcat.name}</option>`
                                );
                            });

                            // Setelah pertama load → jangan pakai initial lagi
                            firstLoad = false;
                        }
                    });
                }
            });

            // Set saat edit
            $('#category_id').val(initialCategoryId).trigger('change');

        });
    </script>
@endpush
