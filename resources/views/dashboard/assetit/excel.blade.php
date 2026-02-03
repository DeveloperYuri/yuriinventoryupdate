<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: left;">
                Daftar Asset IT
            </th>
        </tr>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Category Asset</th>
            <th style="text-align: center;">User</th>
            <th style="text-align: center;">Lokasi</th>
            <th style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assetit as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->nama }}</td>
                <td style="text-align: center;">{{ $item->user }}</td>
                <td style="text-align: center;">{{ $item->location->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
