<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center;">
                History Spare Part In / Out
            </th>
        </tr>

        <tr>
            <th colspan="5">
                Periode:
                @if ($startDate && $endDate)
                    {{ $startDate }} s/d {{ $endDate }}
                @elseif ($startDate)
                    {{ $startDate }} s/d Hari Ini
                @elseif ($endDate)
                    Dari Awal s/d {{ $endDate }}
                @else
                    -
                @endif
            </th>
        </tr>

        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama ATK</th>
            <th style="text-align: center;">User</th>
            <th style="text-align: center;">Jenis</th>
            <th style="text-align: center;">Jumlah</th>
            <th style="text-align: center;">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->atk->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->user }}</td>
                <td style="text-align: center;">{{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: center;">{{ $item->created_at->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
