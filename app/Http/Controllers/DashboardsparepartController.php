<?php

namespace App\Http\Controllers;

use App\Exports\DashboardSparepartExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class DashboardsparepartController extends Controller
{
    // DashboardController.php
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('m')); // default bulan ini
        $tahun = $request->input('tahun', date('Y'));

        // Total jenis spare part per bulan 
        $totalJenisBulanIni = \App\Models\ListSparePartModel::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        // Total masuk dan keluar
        $totalMasukBulanIni = \App\Models\StockTransactionModel::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('type', 'in')
            ->sum('quantity');

        $totalKeluarBulanIni = \App\Models\StockTransactionModel::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->where('type', 'out')
            ->sum('quantity');

        // Top 5 spare part paling sering dipakai
        $top5 = \App\Models\StockTransactionModel::select('spare_part_id')
            ->selectRaw('SUM(quantity) as total')
            ->whereRaw('LOWER(type) = ?', ['out'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('spare_part_id')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->with('sparePart')
            ->limit(5)
            ->get();

        // dd($top5);

        // Stok menipis
        $minimum = 1;

        $stokMenipis = \App\Models\ListSparePartModel::where('stock', '<=', $minimum)
            ->orderBy('stock', 'asc')
            ->paginate(5)
            ->onEachSide(2);

        // query insight per bulan
        $totalTop5 = $top5->sum('total');
        $persentaseTop5 = $totalKeluarBulanIni > 0
            ? round(($totalTop5 / $totalKeluarBulanIni) * 100)
            : 0;

        return view('dashboard.dashboardsparepart.index', compact('totalJenisBulanIni', 'bulan', 'tahun', 'totalMasukBulanIni', 'totalKeluarBulanIni', 'top5', 'stokMenipis', 'persentaseTop5'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        $minimum = 1;

        // TOTAL JENIS
        $totalJenisBulanIni = \App\Models\ListSparePartModel::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        // TOTAL MASUK
        $totalMasukBulanIni = \App\Models\StockTransactionModel::where('type', 'in')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->sum('quantity');

        // TOTAL KELUAR
        $totalKeluarBulanIni = \App\Models\StockTransactionModel::where('type', 'out')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->sum('quantity');

        // TOP 5
        $top5 = \App\Models\StockTransactionModel::select('spare_part_id')
            ->selectRaw('SUM(quantity) as total')
            ->where('type', 'out')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('spare_part_id')
            ->orderByDesc('total')
            ->with('sparePart')
            ->limit(5)
            ->get();

        // STOK MENIPIS
        $minimum = 1;

        $stokMenipis = \App\Models\ListSparePartModel::where('stock', '<=', $minimum)
            ->orderBy('stock', 'asc')
            ->get();

        // dd(
        //     $stokMenipis->map(fn($i) => [
        //         'name'  => $i->name,
        //         'stock' => $i->stock,
        //         'type'  => gettype($i->stock),
        //     ])
        // );

        // $stokMenipis = \App\Models\ListSparePartModel::where('stock', '<=', $minimum)
        //     ->orderBy('stock')
        //     ->get();

        // ✅ INI YANG BENAR
        $data = compact(
            'bulan',
            'tahun',
            'totalJenisBulanIni',
            'totalMasukBulanIni',
            'totalKeluarBulanIni',
            'top5',
            'stokMenipis'
        );

        return Excel::download(
            new DashboardSparepartExport($data),
            "dashboard-sparepart-{$bulan}-{$tahun}.xlsx"
        );
    }
}
