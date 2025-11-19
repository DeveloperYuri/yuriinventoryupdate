<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: left;">
                Daftar Stok ATK
            </th>
        </tr>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama</th>
            <th style="text-align: center;">Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($atk as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->name }}</td>
                <td style="text-align: center;">{{ $item->stock }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
