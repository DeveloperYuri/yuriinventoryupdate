@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Asset Tools</h5>

                            <!-- Horizontal Form -->
                            <form action="{{ route('asset-tools.update', $assetTools->id)}}" method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                {{ csrf_field() }}

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">IMAGE</label>
                                    <div class="col-sm-10">
                                        @if($assetTools->image)
                                        <img src="{{ asset('images/'.$assetTools->image) }}" alt="{{ $assetTools->name }}" width="100">
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                    <br >
                                    <label class="mt-2" >Ganti Gambar (Opsional)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Name<span
                                        style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="name" value="{{ $assetTools->name }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Price<span
                                        style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputText" name="price" value="{{ $assetTools->price }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                                    <div class="col-sm-10">
                                        <select name="satuan" class="form-control" required dusk="sparepartintoggle"
                                            value="{{ $assetTools->satuan }}">
                                            <option value="Pcs" {{ $assetTools->satuan == 'Pcs' ? 'selected' : '' }}>Pcs
                                            </option>
                                            <option value="Pack" {{ $assetTools->satuan == 'Pack' ? 'selected' : '' }}>Pack
                                            </option>
                                            <option value="Meter" {{ $assetTools->satuan == 'Meter' ? 'selected' : '' }}>
                                                Meter</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="{{ route('asset-tools.index')}}" class="btn btn-secondary">Back</a>
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
