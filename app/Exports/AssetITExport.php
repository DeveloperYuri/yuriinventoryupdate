<?php

namespace App\Exports;

use App\Models\AssetitModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AssetITExport implements FromView
{
    public function view(): View
    {
        return view('dashboard.assetit.excel', [
            'assetit' => AssetitModel::with('location')->get()
        ]);
    }
}
