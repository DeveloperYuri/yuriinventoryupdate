<?php

namespace App\Http\Controllers;

use App\Models\ItemAllModel;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function autocomplete(Request $request)
    {
        return ItemAllModel::where('name', 'like', "%{$request->q}%")
            ->limit(10)
            ->get()
            ->map(function ($item) {

                // DEFAULT
                $stock = 0;

                // HANYA SPAREPART YANG PUNYA STOK
                if ($item->item_type === 'sparepart') {
                    $stock = (int) ($item->stock ?? 0);
                }

                return [
                    'id'        => $item->id,
                    'label'     => '[' . strtoupper($item->item_type) . '] ' . $item->name,
                    'value'     => $item->name,
                    'item_type' => $item->item_type,
                    'stock'     => $stock, 
                ];
            });
    }
}
