<table style="border-collapse: collapse; width: 100%;" border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: center;">
                History Asset Tools: {{ $assetTools->name }}
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
            <th style="text-align: center;">Jenis</th>
            <th style="text-align: center;">Jumlah</th>
            <th style="text-align: center;">User</th>
            <th style="text-align: center;">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @php $totalStock = 0; @endphp
        @foreach ($transactions as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: center;">{{ $item->user ?? '-' }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
            </tr>
            @php
                $totalStock += $item->type === 'in' ? $item->quantity : -$item->quantity;
            @endphp
        @endforeach
        <tr>
            <td colspan="2"><strong>Jumlah Akhir Stok</strong></td>
            <td style="text-align: center;"><strong>{{ $totalStock }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
