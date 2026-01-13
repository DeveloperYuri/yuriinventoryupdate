<?php

namespace App\Http\Controllers;

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

        return view('dashboard.dashboardsparepart.index', compact('totalJenisBulanIni', 'bulan', 'tahun', 'totalMasukBulanIni', 'totalKeluarBulanIni'));



        // // Top 5 spare part paling sering dipakai
        // $top5 = \App\Models\StockTransactionModel::select('spare_part_id')
        //     ->selectRaw('SUM(quantity) as total')
        //     ->whereYear('created_at', $tahun)
        //     ->whereMonth('created_at', $bulan)
        //     ->where('type', 'keluar')
        //     ->groupBy('spare_part_id')
        //     ->orderByDesc('total')
        //     ->with('sparePart') // relasi ke SparePart
        //     ->take(5)
        //     ->get();

        // // Stok menipis
        // $stokMenipis = \App\Models\SparePartModel::whereColumn('stock', '<=', 'min_stock')->get();

        // return view('dashboard.sparepart', compact(
        //     'totalJenis',
        //     'totalMasuk',
        //     'totalKeluar',
        //     'top5',
        //     'stokMenipis',
        //     'bulan',
        //     'tahun'
        // ));
    }


    // public function index(){
    //     return view('dashboard.dashboardsparepart.index');
    // }
}
