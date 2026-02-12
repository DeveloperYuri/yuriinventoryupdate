<!DOCTYPE html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Rekap Pesanan Barang</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Spare Part</th>
                <th>Qty</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop menggunakan variabel yang dikirim dari Controller --}}
            @foreach($data_barang as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    {{-- Panggil relasi sparePart (sesuai nama fungsi di model kamu) --}}
                    <td>{{ $item->sparePart->name ?? 'N/A' }}</td> 
                    <td>{{ $item->qty_kurang }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>