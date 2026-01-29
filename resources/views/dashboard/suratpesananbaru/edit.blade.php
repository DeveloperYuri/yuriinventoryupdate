@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <h2 class="mt-4">Edit Surat Pesanan</h2>

                            <form id="myForm" class="mt-4"
                                action="{{ route('suratpesananbaru.update', $transaction->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">

                                    <!-- KIRI -->
                                    <div class="col-md-6">

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">No SP</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"
                                                    value="{{ $transaction->no_surat_pesanan }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Di buat oleh</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', $transaction->name) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Department</label>
                                            <div class="col-sm-8">
                                                <select id="department_id" name="department_id" class="form-control">
                                                    <option value="">-- Pilih Department --</option>
                                                    @foreach ($departments as $d)
                                                        <option value="{{ $d->id }}"
                                                            {{ old('department_id', $transaction->department_id) == $d->id ? 'selected' : '' }}>
                                                            {{ $d->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Category</label>
                                            <div class="col-sm-8">
                                                <select id="category_id" name="category_id" class="form-control">
                                                    <option value="">-- Pilih Category --</option>
                                                    @foreach ($categories as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ old('category_id', $transaction->category_id) == $c->id ? 'selected' : '' }}>
                                                            {{ $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- KANAN -->
                                    <div class="col-md-6">

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" name="created_at"
                                                    value="{{ old('created_at', $transaction->created_at->format('Y-m-d')) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Lokasi</label>
                                            <div class="col-sm-10">
                                                <select name="locations_id" class="form-control">
                                                    @foreach ($locations as $l)
                                                        <option value="{{ $l->id }}"
                                                            {{ old('locations_id', $transaction->locations_id) == $l->id ? 'selected' : '' }}>
                                                            {{ $l->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Sub Category</label>
                                            <div class="col-sm-8">
                                                <select id="subcategory_id" name="subcategory_id" class="form-control">
                                                    @foreach ($subcategories as $s)
                                                        <option value="{{ $s->id }}"
                                                            {{ old('subcategory_id', $transaction->subcategory_id) == $s->id ? 'selected' : '' }}>
                                                            {{ $s->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Qty_Minta</th>
                                            <th>Stok</th>
                                            <th>Qty_Kurang</th>
                                            <th>Keterangan</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="productTableBody">

                                        @foreach ($transaction->details as $d)
                                            <tr>
                                                <td>
                                                    <input type="text" name="product_name[]"
                                                        class="form-control product-name"
                                                        value="{{ $d->item->name ?? $d->product_name }}">
                                                    <input type="hidden" name="item_id[]" class="item-id"
                                                        value="{{ $d->item_id }}">
                                                    <input type="hidden" name="item_type[]" class="item-type"
                                                        value="{{ $d->item_type }}">
                                                </td>

                                                <td><input type="number" name="demand[]" class="form-control qty"
                                                        value="{{ $d->qty }}"></td>
                                                <td><input type="number" name="stock[]" class="form-control stok" readonly
                                                        value="{{ $d->stock }}"></td>
                                                <td><input type="number" name="qty_kurang[]"
                                                        class="form-control qty-kurang" readonly
                                                        value="{{ $d->qty_kurang }}"></td>
                                                <td><input type="text" name="keterangan[]" class="form-control"
                                                        value="{{ $d->keterangan }}"></td>
                                                <td><button type="button"
                                                        class="btn btn-danger btn-sm removeLine">Remove</button></td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="4"><a href="#" id="addLineBtn">Tambah Barang</a></td>
                                        </tr>

                                    </tbody>
                                </table>

                                <button class="btn btn-primary">Update</button>
                                <a href="{{ route('suratpesananbaru.index') }}" class="btn btn-secondary">Cancel</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
