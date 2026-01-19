<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: left;">
                Daftar Asset Tools
            </th>
        </tr>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama Asset Tools</th>
            <th style="text-align: center;">Digunakan oleh</th>
            <th style="text-align: center;">Lokasi</th>
            {{-- <th style="text-align: center;">Stok</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($assettools as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->name }}</td>
                <td style="text-align: center;">{{ $item->latestStockTransaction?->user ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->latestStockTransaction?->location?->name ?? '-' }}</td>
                {{-- <td style="text-align: center;">{{ $item->stock }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
