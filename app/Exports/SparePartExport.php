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
        // $spareparts = ListSparePartModel::all();
        $spareparts = ListSparePartModel::whereHas('produkstatus', function ($q) {
            $q->where('name', 'Active');
        })->get();

        if ($this->period) {
            // MODE BULANAN
            $start = $this->period . '-01';

            // GUNAKAN DateTime agar jam 23:59:59 tercover
            $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString();

            foreach ($spareparts as $part) {
                // Pastikan method getInBefore & getOutBefore di Model 
                // menggunakan operator '<=' terhadap $prevMonthEnd (23:59:59)
                // Atau langsung hitung manual seperti di Controller index:

                $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();

                $stockAwal = $part->transactions()
                    ->where('created_at', '<=', $prevMonthEnd)
                    ->selectRaw("SUM(CASE WHEN type='in' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;

                $masuk  = $part->getInPeriod($start, $end);
                $keluar = $part->getOutPeriod($start, $end);

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
            'period' => $this->period
        ]);
    }
}
