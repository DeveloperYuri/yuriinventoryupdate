<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: left;">
                Daftar Spare Part Masuk
            </th>
        </tr>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama Sparepart</th>
            <th style="text-align: center;">Jumlah</th>
            <th style="text-align: center;">Tanggal Masuk</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stockIns as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->sparePart->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: center;">{{ $item->created_at->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
