<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class AssetToolsHistoryPerItemExport implements FromView
{
    protected $assetTools;
    protected $transactions;
    protected $startDate;
    protected $endDate;

    public function __construct($assetTools, $transactions, $startDate = null, $endDate = null)
    {
        $this->assetTools = $assetTools;
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('assettoolsexcel.assettools_history_peritem', [
            'assetTools' => $this->assetTools,
            'transactions' => $this->transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
