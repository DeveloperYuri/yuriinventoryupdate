<?php

namespace App\Exports;

use App\Models\AtktransactionModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AtkHistoryExport implements FromView
{
    protected $start_date;
    protected $end_date;
    protected $type;

    public function __construct($start_date = null, $end_date = null, $type = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->type       = $type;
    }

    public function view(): View
    {
        
        $query = AtktransactionModel::with('atk')->orderBy('created_at', 'asc');

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        if ($this->type) {
            $query->where('type', $this->type); 
        }

        $transactions = $query->get();

        return view('dashboard.atk.atkexcel.atkhistory', [
            'transactions' => $transactions,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
        ]);
    }
}
