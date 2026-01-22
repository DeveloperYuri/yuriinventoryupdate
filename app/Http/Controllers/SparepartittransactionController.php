<?php

namespace App\Http\Controllers;

use App\Models\SparepartittrasactionModel;
use Illuminate\Http\Request;

class SparepartittransactionController extends Controller
{
    public function history(Request $request)
    {
        $query = SparepartittrasactionModel::with('sparePart')->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('dashboard.sparepartit.riwayatsparepartit.index', compact('transactions'));
    }
}
