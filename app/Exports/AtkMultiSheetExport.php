<?php

namespace App\Exports;

use App\Models\CategoryModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AtkMultiSheetExport implements WithMultipleSheets

{
    protected $period;

    public function __construct($period)
    {
        // Kita tangkap periode dari Controller (misal: 2024-05)
        $this->period = $period;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Ambil kategori yang ada di database
        // $categories = CategoryModel::all();
        // $categories = CategoryModel::has('spareparts')->get();
        $categories = CategoryModel::whereHas('atk.produkstatus', function ($q) {
            $q->where('name', 'Active');
        })->get();

        foreach ($categories as $category) {
            $sheets[] = new AtkPerCategorySheet($category, $this->period);
        }

        // 2. TAMBAHKAN SHEET UNTUK YANG TANPA KATEGORI (NULL)
        // Kita kirim null sebagai pengganti object category
        $sheets[] = new AtkPerCategorySheet(null, $this->period);

        return $sheets;
    }
}
