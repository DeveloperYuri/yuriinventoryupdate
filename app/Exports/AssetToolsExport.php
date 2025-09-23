<?php

namespace App\Exports;

use App\Models\ListAssetToolsModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\ListSparePartModel;

class AssetToolsExport implements FromView
{
    public function view(): View
    {
        return view('assettoolsexcel.assettool', [
            'assettools' => ListAssetToolsModel::all()
        ]);
    }
}