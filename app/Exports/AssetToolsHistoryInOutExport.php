<?php

namespace App\Exports;

use App\Models\StockAssetTransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssetToolsHistoryInOutExport implements FromView
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date = null, $end_date = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function view(): View
    {
        $query = StockAssetTransactionModel::with('assetTools')->orderByDesc('created_at');

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        $transactions = $query->get();

        return view('assettoolsexcel.assettools_history_inout', [
            'transactions' => $transactions,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
        ]);
    }
}
