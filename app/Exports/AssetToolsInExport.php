<?php

namespace App\Exports;

use App\Models\StockAssetTransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssetToolsInExport implements FromView
{
    public function view(): View
    {
        $assetstockIns = StockAssetTransactionModel::where('type', 'in')->with('assetTools')->get();
        return view('assettoolsexcel.assettools_in', compact('assetstockIns'));
    }
}
