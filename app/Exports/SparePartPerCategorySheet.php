<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ListSparePartModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SparePartPerCategorySheet implements FromView, WithTitle, ShouldAutoSize
{
    private $category;
    private $period;

    /**
     * Data dikirim dari SparePartMultiSheetExport
     */
    public function __construct($category, $period)
    {
        $this->category = $category;
        $this->period = $period;
    }

    public function title(): string
    {
        // Jika category null, nama sheet jadi "Tanpa Kategori" atau "-"
        return $this->category ? $this->category->name : 'Tanpa Kategori';
    }

    public function view(): View
    {
        // Jika category ada, filter berdasarkan ID. Jika null, cari yang category_id nya NULL
        if ($this->category) {
            $spareparts = ListSparePartModel::where('category_id', $this->category->id)->get();
            $categoryName = $this->category->name;
        } else {
            $spareparts = ListSparePartModel::whereNull('category_id')->get();
            $categoryName = '-'; // Atau tulis 'KOSONG'
        }

        $period = $this->period;

        // Logika Perhitungan Stok (Sama dengan di Index)
        if ($period) {
            $start = $period . '-01';
            $end   = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString();
            $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();

            foreach ($spareparts as $part) {
                // Hitung Saldo awal sebelum periode yang dipilih
                $stockAwal = $part->transactions()
                    ->where('created_at', '<=', $prevMonthEnd)
                    ->selectRaw("SUM(CASE WHEN type='in' THEN quantity ELSE -quantity END) as balance")
                    ->value('balance') ?? 0;

                $part->stock_awal = $stockAwal;
                $part->masuk      = $part->getInPeriod($start, $end);
                $part->keluar     = $part->getOutPeriod($start, $end);
                $part->stock_akhir = $stockAwal + $part->masuk - $part->keluar;
            }
        } else {
            // Jika tidak ada periode (Global)
            foreach ($spareparts as $part) {
                $part->stock_awal = 0;
                $part->masuk      = $part->getTotalIn();
                $part->keluar     = $part->getTotalOut();
                $part->stock_akhir = $part->masuk - $part->keluar;
            }
        }

        return view('sparepartexcel.multipleexcel', [
            'getRecord' => $spareparts,
            'period'    => $period,
            'categoryName' => $categoryName // Kirim nama kategori manual ke blade
        ]);
    }
}
