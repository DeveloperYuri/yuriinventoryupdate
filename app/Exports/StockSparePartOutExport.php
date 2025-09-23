<?php

namespace App\Exports;

use App\Models\StockTransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockSparePartOutExport implements FromView
{
    public function view(): View
    {
        $stockOut = StockTransactionModel::where('type', 'out')->with('sparePart')->get();
        return view('sparepartexcel.sparepart_out', compact('stockOut'));
    }
}
