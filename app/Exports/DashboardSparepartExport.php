<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class DashboardSparepartExport implements FromArray, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // ================= HEADER =================
        $rows[] = ['LAPORAN DASHBOARD SPARE PART PER BULAN'];
        $rows[] = ['Periode', $this->data['bulan'] . ' / ' . $this->data['tahun']];
        $rows[] = [];
        $rows[] = [];
        $rows[] = [];

        // ================= KPI =================
        $rows[] = ['RINGKASAN'];
        $rows[] = ['Total Jenis Spare Part Baru', $this->data['totalJenisBulanIni']];
        $rows[] = ['Total Masuk (PCS)', $this->data['totalMasukBulanIni']];
        $rows[] = ['Total Keluar (PCS)', $this->data['totalKeluarBulanIni']];
        $rows[] = [];

        // ================= TOP 5 =================
        $rows[] = ['TOP 5 SPARE PART PALING SERING DIPAKAI'];
        $rows[] = ['No', 'Spare Part', 'Total Dipakai (PCS)'];

        foreach ($this->data['top5'] as $i => $item) {
            $rows[] = [
                $i + 1,
                optional($item->sparePart)->name,
                $item->total
            ];
        }

        $rows[] = [];

        // ================= STOK MENIPIS =================
        $rows[] = ['SPARE PART DENGAN STOK KOSONG & MENIPIS'];
        $rows[] = ['No', 'Spare Part', 'Stok', 'Minimum', 'Status'];

        foreach ($this->data['stokMenipis'] as $i => $item) {
            if ($item->stock === 0) {
                $status = 'Habis';
            } else {
                $status = 'Menipis';
            }

            $rows[] = [
                $i + 1,
                $item->name,
                // (int) $item->stock, // 
                $item->stock === 0 ? '0 ' : $item->stock,
                1,
                $status
            ];
        }


        // foreach ($this->data['stokMenipis'] as $i => $item) {
        //     $rows[] = [
        //         $i + 1,
        //         $item->name,
        //         $item->stock,
        //         1,
        //         'Menipis'
        //     ];
        // }

        return $rows;
    }

    public function title(): string
    {
        return 'Dashboard';
    }
}
