@extends('dashboard.layouts.main')

@section('content')
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-12">
                    <div class="row">

                        <!-- Spare Part Card -->
                        @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
                            <div class="col-xxl-4 col-md-4">
                                <div class="card info-card sales-card">
                                    <a href="{{ route('spare-parts.index') }}">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Spare Part</h5>

                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-wrench"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $getSparepart }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <!-- End Spare Part Card -->

                        <!-- Asset Card -->
                        @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
                            <div class="col-xxl-4 col-md-4">
                                <div class="card info-card sales-card">
                                    <a href="{{ route('asset-tools.index') }}">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Asset Tools</h5>

                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-tools"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $getAssettools }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <!-- End Asset Card -->

                        <!-- Sales Card -->
                        <div class="col-xxl-4 col-md-4">
                            <div class="card info-card sales-card">
                                <a href="{{ route('indexsupplier') }}">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Supplier</h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-truck"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $getSupplier }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div><!-- End Sales Card -->

                    </div>
                </div><!-- End Left side columns -->

            </div>
        </section>

    </main><!-- End #main -->
@endsection
