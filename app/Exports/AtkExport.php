<?php

namespace App\Exports;

use App\Models\AtkModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AtkExport implements FromView
{
    
    public function view(): View
    {
        return view('dashboard.atk.laporanatkexcel', [
            'atk' => AtkModel::all()
        ]);
    }
}

