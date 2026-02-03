<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\ListSparePartModel;
use Illuminate\Support\Facades\DB;

class SparepartExport implements FromView
{

    protected $period;

    public function __construct($period = null)
    {
        $this->period = $period;
    }

    public function view(): View
    {
        $spareparts = ListSparePartModel::all();

        if ($this->period) {
            // MODE BULANAN
            $start = $this->period . '-01';
            $end   = \Carbon\Carbon::parse($start)->endOfMonth()->toDateString();

            foreach ($spareparts as $part) {
                $stockAwal = $part->getInBefore($start) - $part->getOutBefore($start);
                $masuk     = $part->getInPeriod($start, $end);
                $keluar    = $part->getOutPeriod($start, $end);

                $part->stock_awal  = $stockAwal;
                $part->masuk       = $masuk;
                $part->keluar      = $keluar;
                $part->stock_akhir = $stockAwal + $masuk - $keluar;
            }
        } else {
            // MODE GLOBAL (tanpa periode)
            foreach ($spareparts as $part) {
                $masuk  = $part->getTotalIn();
                $keluar = $part->getTotalOut();

                $part->stock_awal  = 0;
                $part->masuk       = $masuk;
                $part->keluar      = $keluar;
                $part->stock_akhir = $masuk - $keluar;
            }
        }

        return view('sparepartexcel.sparepart', [
            'spareparts' => $spareparts,
            'period' => $this->period   // ← INI yang kurang
        ]);

        // return view('sparepartexcel.sparepart', compact('spareparts'));
    }

    // protected $period;

    // public function __construct($period)
    // {
    //     $this->period = $period;
    // }

    // public function view(): View
    // {
    //     $start = $this->period . '-01';
    //     $end   = \Carbon\Carbon::parse($start)->endOfMonth()->toDateString();

    //     $spareparts = ListSparePartModel::all();

    //     foreach ($spareparts as $part) {
    //         $stockAwal = $part->getInBefore($start) - $part->getOutBefore($start);
    //         $masuk     = $part->getInPeriod($start, $end);
    //         $keluar    = $part->getOutPeriod($start, $end);

    //         $part->stock_awal  = $stockAwal;
    //         $part->masuk       = $masuk;
    //         $part->keluar      = $keluar;
    //         $part->stock_akhir = $stockAwal + $masuk - $keluar;
    //     }

    //     return view('sparepartexcel.sparepart', compact('spareparts'));
    // }
    // public function view(): View
    // {
    //     return view('sparepartexcel.sparepart', [
    //         'spareparts' => ListSparePartModel::all()
    //     ]);
    // }
}
