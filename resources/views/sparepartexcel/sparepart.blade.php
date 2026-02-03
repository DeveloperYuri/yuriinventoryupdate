<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: left;">
                Daftar Spare Part
            </th>
        </tr>

        <tr>
            <th colspan="6" style="text-align: left;">
                @if ($period)
                    Periode: {{ \Carbon\Carbon::parse($period . '-01')->translatedFormat('F Y') }}
                @else
                    Periode: Semua Periode (Stok Global)
                @endif
            </th>
        </tr>

        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama</th>
            <th style="text-align: center;">Stok Awal (PCS)</th>
            <th style="text-align: center;">Jumlah Masuk (PCS)</th>
            <th style="text-align: center;">Jumlah Keluar (PCS)</th>
            <th style="text-align: center;">Stok Akhir (PCS)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($spareparts as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->name }}</td>
                <td class="text-center">{{ $item->stock_awal }}</td>
                <td class="text-center">{{ $item->masuk }}</td>
                <td class="text-center">{{ $item->keluar }}</td>
                <td class="text-center">{{ $item->stock_akhir }}</td>
                {{-- <td style="text-align: center;">{{ $item->getTotalIn() }}</td>
                <td style="text-align: center;">{{ $item->getTotalOut() }}</td>
                <td style="text-align: center;">{{ $item->stock }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
