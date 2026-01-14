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

    <form class="d-flex gap-2 align-items-end"
      method="GET"
      action="{{ route('dashboardsparepart.index') }}">

    <!-- Pilih Bulan -->
    <div>
        <select name="bulan" class="form-select form-select-sm" style="width: 150px;">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
            @endfor
        </select>
    </div>

    <!-- Pilih Tahun -->
    <div>
        <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
            @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">
        Tampilkan
    </button>

    <a href="{{ route('dashboardsparepart.export.excel', request()->all()) }}"
       class="btn btn-success btn-sm">
        ⬇ Export Excel
    </a>

</form>


    {{-- <form class="d-flex gap-2" method="GET" action="{{ route('dashboardsparepart.index') }}">
        
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
        <div>
<a href="{{ route('dashboardsparepart.export.excel', request()->all()) }}"
   class="btn btn-success btn-sm">
   ⬇ Export Excel
</a>

        </div>
        

    </form> --}}
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
                {{-- <pre>{{ print_r($top5->toArray(), true) }}</pre> --}}

                <canvas id="chartTop5" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- TABLE + INSIGHT -->
    <div class="row g-4">
        <div class="col-md-7">
    <div class="card p-4">
        {{-- <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Spare Part dengan Stok Menipis</h6>
            <a href="{{ route('sparepart.export.menipis') }}" class="btn btn-success btn-sm">
                ⬇ Export Excel
            </a>
        </div> --}}

        <h6 class="fw-bold mb-0">Spare Part dengan Stok kosong & Menipis</h6>

        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th class="text-center">Spare Part</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Minimum</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
@forelse ($stokMenipis as $item)
    <tr>
        <td class="text-center">{{ $item->name }}</td>

        <td class="fw-bold text-center
            {{ $item->stock == 0 ? 'text-danger' : 'text-warning' }}">
            {{ $item->stock }}
        </td>

        <td class="text-center">1</td>

        <td class="text-center">
            @if ($item->stock == 0)
                <span class="badge bg-danger">
                    Kosong
                </span>
            @elseif ($item->stock == 1)
                <span class="badge bg-warning text-dark">
                    Menipis
                </span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center text-muted">
            Tidak ada stok menipis
        </td>
    </tr>
@endforelse
</tbody>
            {{-- <tbody>
                @forelse ($stokMenipis as $item)
                    <tr>
                        <td class="text-center">{{ $item->name }}</td>
                        <td class="fw-bold text-center text-danger">{{ $item->stock }}</td>
                        <td class="text-center" >{{ $minimum = 1 }}</td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">
                                Menipis
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Tidak ada stok menipis
                        </td>
                    </tr>
                @endforelse
            </tbody> --}}
        </table>
        @if ($stokMenipis->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $stokMenipis->links('pagination::bootstrap-5') }}
    </div>
@endif

    </div>
</div>

<div class="col-md-5">
    <div class="card p-4">
        <h6 class="fw-bold mb-3">Insight Bulanan</h6>

        @if ($top5->count())
            <div class="alert alert-warning mb-0">
                <strong>Top 5 spare part paling sering digunakan bulan ini:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($top5 as $item)
                        <li>
                            {{ optional($item->sparePart)->name ?? 'Unknown' }}
                            (<strong>{{ $item->total }} PCS</strong>)
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="alert alert-info mb-0 text-center">
                Tidak ada data pemakaian spare part bulan ini
            </div>
        @endif

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

    // 🔹 Data dari Controller
    const top5Labels = {!! json_encode($top5->pluck('sparePart.name')) !!};
    const top5Data   = {!! json_encode($top5->pluck('total')) !!};

    // 🔹 Deteksi data kosong
    const isEmpty = top5Data.length === 0;

    // 🔹 Fallback data jika kosong
    const labels = isEmpty ? ['Tidak ada data'] : top5Labels;
    const data   = isEmpty ? [0] : top5Data;

    const maxValue = isEmpty ? 1 : Math.max(...data);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Dipakai (PCS)',
                data: data,
                borderRadius: 8
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: !isEmpty
                },
                title: {
                    display: isEmpty,
                    text: 'Tidak ada data pemakaian spare part pada bulan ini'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: isEmpty ? 1 : maxValue + 10,
                    ticks: {
                        stepSize: isEmpty ? 1 : undefined
                    }
                }
            }
        }
    });
});
</script>
@endpush


