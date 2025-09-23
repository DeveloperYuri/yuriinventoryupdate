<?php

namespace App\Imports;

use App\Models\ListSparePartModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SparePartImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1; // Pastikan mulai dari baris pertama
    }

    public function model(array $row)
    {
        if (empty($row['name'])) return null;

        return new ListSparePartModel([
            'name'   => $row['name'],
            'price'  => $row['price'],
            'stock'  => $row['stock'],
            'satuan' => $row['satuan'],
        ]);
    }
}