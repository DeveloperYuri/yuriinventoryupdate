<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\ListSparePartModel;

class SparepartExport implements FromView
{
    public function view(): View
    {
        return view('sparepartexcel.sparepart', [
            'spareparts' => ListSparePartModel::all()
        ]);
    }
}

