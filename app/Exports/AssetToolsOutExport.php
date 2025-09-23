<?php

namespace App\Exports;

use App\Models\StockAssetTransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssetToolsOutExport implements FromView
{
    public function view(): View
    {
        $assetstockOut = StockAssetTransactionModel::where('type', 'out')->with('assetTools')->get();
        return view('assettoolsexcel.assettools_out', compact('assetstockOut'));
    }
}
