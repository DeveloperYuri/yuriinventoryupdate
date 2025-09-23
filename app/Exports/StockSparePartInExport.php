<?php

namespace App\Exports;

use App\Models\StockTransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockSparePartInExport implements FromView
{
    public function view(): View
    {
        $stockIns = StockTransactionModel::where('type', 'in')->with('sparePart')->get();
        return view('sparepartexcel.sparepart_in', compact('stockIns'));
    }
}

