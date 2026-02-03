<table>
    <thead>
        {{-- Header Judul --}}
        <tr>
            <th colspan="7" style="font-weight: bold; text-align: center; font-size: 14pt;">
                LAPORAN MUTASI SPARE PART
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold;">
                KATEGORI: {{ strtoupper($categoryName) }}
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                Periode: {{ $period ? \Carbon\Carbon::parse($period)->format('F Y') : 'Semua Waktu' }}
            </th>
        </tr>
        <tr></tr> {{-- Baris Kosong --}}

        {{-- Header Tabel --}}
        <tr style="background-color: #444444; color: #ffffff; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #000000;">NO</th>
            <th style="border: 1px solid #000000; width: 20px;">PART NUMBER</th>
            <th style="border: 1px solid #000000; width: 40px;">NAMA BARANG</th>
            <th style="border: 1px solid #000000;">STOK AWAL</th>
            <th style="border: 1px solid #000000;">MASUK</th>
            <th style="border: 1px solid #000000;">KELUAR</th>
            <th style="border: 1px solid #000000;">STOK AKHIR</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getRecord as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $item->numbers }}</td>
                <td style="border: 1px solid #000000;">{{ $item->name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->stock_awal ?? 0 }}</td>
                <td style="border: 1px solid #000000; text-align: center; color: #008000;">{{ $item->masuk ?? 0 }}</td>
                <td style="border: 1px solid #000000; text-align: center; color: #FF0000;">{{ $item->keluar ?? 0 }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ $item->stock_akhir ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
