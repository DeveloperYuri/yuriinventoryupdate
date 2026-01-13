@extends('dashboard.layouts.main')

@push('styles')
<style>
    body { background:#f5f6f8; }
    .kpi-card {
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .kpi-value {
        font-size: 34px;
        font-weight: bold;
    }
    .card {
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<main id="main" class="main">
<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">📦 Dashboard Laporan Spare Part</h3>

    <form class="d-flex gap-2" method="GET" action="{{ route('dashboardsparepart.index') }}">
        
        <!-- Pilih Bulan -->
        <select name="bulan" class="form-select" style="width: 150px !important;">
    @for ($m = 1; $m <= 12; $m++)
        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
        </option>
    @endfor
</select>

        <!-- Pilih Tahun -->
        <select name="tahun" class="form-select">
            @for ($y = date('Y')-5; $y <= date('Y'); $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>
</div>


    <!-- KPI -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-value text-primary">{{ $totalJenisBulanIni }}</div>
                <div class="text-muted">Total Jenis Spare Part Baru</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-value text-success">{{ $totalMasukBulanIni }}</div>
                <div class="text-muted">Total Spare Part Masuk (PCS)</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card p-3">
                <div class="kpi-value text-danger">{{ $totalKeluarBulanIni }}</div>
                <div class="text-muted">Total Spare Part Keluar (PCS)</div>
            </div>
        </div>
    </div>

    <!-- TOP 5 GRAPH -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card p-4">
                <h5 class="fw-bold mb-3">
                    Top 5 Spare Part Paling Sering Dipakai (PCS)
                </h5>
                <canvas id="chartTop5" height="300"></canvas>

            </div>
        </div>
    </div>

    <!-- TABLE + INSIGHT -->
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Spare Part dengan Stok Menipis</h6>
                    <button class="btn btn-success btn-sm">⬇ Export Excel</button>
                </div>

                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Spare Part</th>
                            <th>Stok</th>
                            <th>Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Fan Belt A-45</td>
                            <td>5</td>
                            <td>10</td>
                            <td><span class="badge bg-warning text-dark">Menipis</span></td>
                        </tr>
                        <tr>
                            <td>Fuse 10A</td>
                            <td>3</td>
                            <td>10</td>
                            <td><span class="badge bg-warning text-dark">Menipis</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card p-4">
                <h6 class="fw-bold mb-3">Insight Bulanan</h6>
                <div class="alert alert-warning mb-0">
                    Top 5 spare part menyumbang <strong>67%</strong> dari total
                    pemakaian bulan ini. Direkomendasikan prioritas pengadaan
                    pada item dengan stok menipis.
                </div>
            </div>
        </div>
    </div>

</div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('chartTop5');
    if (!ctx) return;

    const dataPemakaian = [85, 72, 60, 48, 41];
    const maxValue = Math.max(...dataPemakaian);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Fan Belt A-45',
                'Bearing 6204',
                'Fuse 10A',
                'Relay MY2',
                'Sensor'
            ],
            datasets: [{
                label: 'Total Dipakai (PCS)',
                data: dataPemakaian
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,

                    // 🔹 bikin grafik "lega" ke atas
                    suggestedMax: maxValue + 20,

                    // 🔹 bikin angka Y lebih banyak
                    ticks: {
                        stepSize: 10
                    },

                    title: {
                        display: true,
                        text: 'Jumlah Pemakaian (PCS)'
                    }
                }
            }
        }
    });

});
</script>
@endpush
