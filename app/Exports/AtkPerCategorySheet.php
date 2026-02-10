<?php

namespace App\Exports;

use App\Models\AtkModel;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;


class AtkPerCategorySheet implements FromView, WithTitle, ShouldAutoSize
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
        // 1. Tambahkan filter Active di sini
        if ($this->category) {
            $atk = AtkModel::where('category_id', $this->category->id)
                ->whereHas('produkstatus', function ($q) {
                    $q->where('name', 'Active'); // Filter status Active
                })
                ->get();
            $categoryName = $this->category->name;
        } else {
            $atk = AtkModel::whereNull('category_id')
                ->whereHas('produkstatus', function ($q) {
                    $q->where('name', 'Active'); // Filter status Active
                })
                ->get();
            $categoryName = '-';
        }

        $period = $this->period;

        // Logika Perhitungan Stok (Sama dengan di Index)
        if ($period) {
            $start = $period . '-01';
            $end   = \Carbon\Carbon::parse($start)->endOfMonth()->toDateTimeString();
            $prevMonthEnd = \Carbon\Carbon::parse($start)->subSecond()->toDateTimeString();

            foreach ($atk as $part) {
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
            foreach ($atk as $part) {
                $part->stock_awal = 0;
                $part->masuk      = $part->getTotalIn();
                $part->keluar     = $part->getTotalOut();
                $part->stock_akhir = $part->masuk - $part->keluar;
            }
        }

        return view('dashboard.atk.laporanatkexcelmultiple', [
            'getRecord' => $atk,
            'period'    => $period,
            'categoryName' => $categoryName // Kirim nama kategori manual ke blade
        ]);
    }
}
