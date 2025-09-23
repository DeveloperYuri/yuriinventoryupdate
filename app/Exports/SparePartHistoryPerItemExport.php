<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class SparePartHistoryPerItemExport implements FromView
{
    protected $sparePart;
    protected $transactions;
    protected $startDate;
    protected $endDate;

    public function __construct($sparePart, $transactions, $startDate = null, $endDate = null)
    {
        $this->sparePart = $sparePart;
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('sparepartexcel.sparepart_history_peritem', [
            'sparePart' => $this->sparePart,
            'transactions' => $this->transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
