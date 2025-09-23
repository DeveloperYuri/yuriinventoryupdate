<?php

namespace App\Http\Controllers;

use App\Exports\SparePartHistoryInOutExport;
use App\Models\ListSparePartModel;
use App\Models\StockTransactionModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SparePartHistoryPerItemExport;
use App\Exports\StockSparePartInExport;
use App\Exports\StockSparePartOutExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class StockController extends Controller
{

    public function stockInIndex(Request $request)
    {
        $query = StockTransactionModel::with('sparePart')
            ->where('type', 'in')
            ->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // $transactions = $query->paginate(10);
        $transactions = $query->paginate(10)->withQueryString();


        return view('dashboard.sparepartin.listsparepartin', compact('transactions'));
    }

    // public function stockInIndex()
    // {
    //     $transactions = StockTransactionModel::with('sparePart')->where('type', 'in')->orderByDesc('created_at', 'desc')->paginate(10);
    //     return view('dashboard.sparepartin.listsparepartin', compact('transactions'));
    // }

    public function stockInForm()
    {
        $spareParts = ListSparePartModel::all();
        return view('dashboard.sparepartin.createsparepartin', compact('spareParts'));
    }

    public function storeStockIn(Request $request)
    {
        $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
            'user' => 'required|string|max:255'
        ], [
            'spare_part_id' => 'Spare part harus diisi',
            'quantity' => 'Quantity harus diisi',
            'user' => 'Field user harus diisi',
        ]);

        StockTransactionModel::create([
            'spare_part_id' => $request->spare_part_id,
            'type' => 'in',
            'quantity' => $request->quantity,
            'user' => $request->user
        ]);

        return redirect()->route('stock-in.index')->with('success', 'Stok masuk berhasil dicatat.');
    }

    public function stockOutIndex(Request $request)
    {
        $query = StockTransactionModel::with('sparePart')
            ->where('type', 'out')
            ->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // $transactions = $query->paginate(10);
        $transactions = $query->paginate(10)->withQueryString();


        return view('dashboard.sparepartout.listsparepartout', compact('transactions'));
    }

    // public function stockOutIndex()
    // {
    //     $transactions = StockTransactionModel::with('sparePart')->where('type', 'out')->orderByDesc('created_at', 'desc')->paginate(10);
    //     return view('dashboard.sparepartout.listsparepartout', compact('transactions'));
    // }

    public function stockOutForm()
    {
        $spareParts = ListSparePartModel::all();
        return view('dashboard.sparepartout.createsparepartout', compact('spareParts'));
    }

    public function storeStockOut(Request $request)
    {
        $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
            'user' => 'required|string|max:255'
        ], [
            'spare_part_id' => 'Spare Part wajib dipilih',
            'quantity' => 'Quantity harus diisi',
            'user' => 'Field user harus diisi',
        ]);

        $sparePart = ListSparePartModel::find($request->spare_part_id);

        if ($request->quantity > $sparePart->stock) {
            return back()
                ->withErrors(['quantity' => 'Jumlah keluar melebihi stok yang tersedia (Stok: ' . $sparePart->stock . ').'])
                ->withInput();
        }

        // if ($request->quantity > $sparePart->stock) {
        //     return back()->withErrors('Jumlah keluar melebihi stok yang tersedia.');
        // }

        StockTransactionModel::create([
            'spare_part_id' => $request->spare_part_id,
            'type' => 'out',
            'quantity' => $request->quantity,
            'user' => $request->user
        ]);

        return redirect()->route('stock-out.index')->with('success', 'Stok keluar berhasil dicatat.');
    }

    public function exportStockInPDF()
    {
        $stockIns = StockTransactionModel::where('type', 'in')->with('sparePart')->get();
        $pdf = Pdf::loadView('sparepartpdf.stock_in', compact('stockIns'));
        return $pdf->download('laporan_stok_masuk.pdf');
    }

    public function exportStockInExcel()
    {
        return Excel::download(new StockSparePartInExport, 'laporan_stok_masuk.xlsx');
    }

    public function exportStockOutExcel()
    {
        return Excel::download(new StockSparePartOutExport, 'laporan_stok_keluar.xlsx');
    }

    public function exportStockOutPDF()
    {
        $stockOuts = StockTransactionModel::where('type', 'out')->with('sparePart')->get();
        $pdf = Pdf::loadView('sparepartpdf.stock_out', compact('stockOuts'));
        return $pdf->download('laporan_stok_keluar.pdf');
    }

    public function history(Request $request)
    {
        $query = StockTransactionModel::with('sparePart')->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // $transactions = $query->paginate(20);
        $transactions = $query->paginate(20)->withQueryString();


        return view('dashboard.spareparthistory.history', compact('transactions'));
    }

    public function exportHistoryPDF(Request $request)
    {
        $query = StockTransactionModel::with('sparePart')->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->get();

        $pdf = Pdf::loadView('sparepartpdf.stock_history', compact('transactions'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_riwayat_stok.pdf');
    }

    public function viewHistoryPerItem(Request $request, $id)
    {
        $sparePart = ListSparePartModel::findOrFail($id);

        $query = StockTransactionModel::with([
            'stockOutHeader.location',
            'stockOutHeader.category',
            'stockOutHeader.subcategory'
        ])->where('spare_part_id', $id);

        // $query = StockTransactionModel::where('spare_part_id', $id);

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $allTransactions = $query->orderBy('created_at')->get();

        $totalStock = 0;
        $runningValue = 0;

        // Hitung total stock dan total value
        $allTransactions->each(function ($item) use (&$totalStock, &$runningValue, $sparePart) {
            if ($item->type === 'in') {
                $totalStock += $item->quantity;
                $runningValue += $item->quantity * $item->price;
            } else {
                $totalStock -= $item->quantity;
                $runningValue -= $item->quantity * $item->price;
            }
            $item->runningStock = $totalStock; // simpan ke tiap item supaya bisa tampil di view
            $item->runningValue = $runningValue; // simpan total harga
        });

        $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('dashboard.sparepart.detailsparepart', compact('sparePart', 'transactions', 'totalStock', 'allTransactions'));
    }

    // public function viewHistoryPerItem(Request $request, $id)
    // {
    //     $sparePart = ListSparePartModel::findOrFail($id);

    //     $query = StockTransactionModel::where('spare_part_id', $id);

    //     if ($request->start_date) {
    //         $query->whereDate('created_at', '>=', $request->start_date);
    //     }

    //     if ($request->end_date) {
    //         $query->whereDate('created_at', '<=', $request->end_date);
    //     }

    //     // Perhitungan total dari semua data (tidak terpengaruh pagination)
    //     $allTransactions = $query->get();
    //     $totalStock = $allTransactions->reduce(function ($carry, $item) {
    //         return $carry + ($item->type === 'in' ? $item->quantity : -$item->quantity);
    //     }, 0);

    //     // Pagination
    //     $transactions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

    //     return view('dashboard.sparepart.detailsparepart', compact('sparePart', 'transactions', 'totalStock'));
    // }

    public function exportHistoryPerItemPDF($id, Request $request)
    {
        $sparePart = ListSparePartModel::findOrFail($id);

        $query = StockTransactionModel::where('spare_part_id', $id)->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Ambil semua transaksi tanpa pagination untuk PDF
        $transactions = $query->get();

        // Perhitungan total stok (tidak terpengaruh pagination)
        $totalStock = $transactions->reduce(function ($carry, $item) {
            return $carry + ($item->type === 'in' ? $item->quantity : -$item->quantity);
        }, 0);

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d F Y') : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d F Y') : null;

        // Buat PDF dengan data transaksi dan total stok
        $pdf = Pdf::loadView('sparepartpdf.detailsparepart', compact('sparePart', 'transactions', 'startDate', 'endDate'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('riwayat_sparepart_' . $sparePart->name . '.pdf');
    }

    public function exportHistoryPerItemExcel($id, Request $request)
    {
        $sparePart = ListSparePartModel::findOrFail($id);
        $query = StockTransactionModel::where('spare_part_id', $id)->orderByDesc('created_at');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->get();

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('d F Y') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('d F Y') : null;

        return Excel::download(
            new SparePartHistoryPerItemExport($sparePart, $transactions, $startDate, $endDate),
            'riwayat_sparepart_peritem' . $sparePart->name . '.xlsx'
        );
    }

    public function exportHistoryExcel(Request $request)
    {
        return Excel::download(
            new SparePartHistoryInOutExport($request->start_date, $request->end_date),
            'laporan_riwayat_sparepartinout.xlsx'
        );
    }
}
