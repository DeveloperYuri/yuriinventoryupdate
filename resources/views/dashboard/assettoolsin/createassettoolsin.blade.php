@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Add New Asset Tools In</h5>

                            <!-- Horizontal Form -->
                            <form id="myForm" action="{{ route('asset-in.store')}}" method="POST">
                                {{ csrf_field() }}

                                {{-- <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Asset Tools</label>
                                    <div class="col-sm-10">
                                        <select name="asset_tools_id" class="form-control" required>
                                            @foreach ($spareParts as $part)
                                                <option value="{{ $part->id }}">{{ $part->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                 <div class="row mb-3">
                                    <label for="asset_tools_name" class="col-sm-2 col-form-label">Asset Tools</label>
                                    <div class="col-sm-10">
                                        <!-- Input untuk user -->
                                        <input type="text" id="asset_tools_name" class="form-control"
                                            placeholder="Ketik nama asset tools">
                                        <!-- Hidden untuk simpan ID -->
                                        <input type="hidden" name="asset_tools_id" id="asset_tools_id">
                                        @error('asset_tools_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Jumlah Masuk</label>
                                    <div class="col-sm-10">
                                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="{{ old('quantity')}}">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="user" class="col-sm-2 col-form-label">Penerima Asset</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="user" id="user" class="form-control @error('user') is-invalid @enderror" value="{{ old('user')}}">
                                        @error('user')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="{{ route('asset-in.index') }}" class="btn btn-secondary">Back</a>
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

@push('styles')
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        /* Tampilan dropdown suggestion */
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
            background: #fff;
            padding: 0;
            margin: 0;
            z-index: 9999;
        }

        .ui-autocomplete li {
            padding: .5rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            list-style: none;
            /* hilangkan bullet */
        }

        .ui-autocomplete li:hover {
            background: #0d6efd;
            color: #fff;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            let assetTools = [
                @foreach ($assetTools as $part)
                    {
                        label: {!! json_encode($part->name) !!},
                        value: {!! json_encode($part->id) !!}
                    },
                @endforeach
            ];

            $("#asset_tools_name").autocomplete({
                source: assetTools,
                minLength: 1,
                select: function(event, ui) {
                    $('#asset_tools_name').val(ui.item.label);
                    $('#asset_tools_id').val(ui.item.value);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li></li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };
        });
    </script>
@endpush

