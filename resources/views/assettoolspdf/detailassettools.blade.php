<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Stok Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        hr {
            border: 1px solid black;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer .left {
            float: left;
        }

        .footer .right {
            float: right;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('assets/img/logobaru.png') }}" alt="Logo Perusahaan">
        <h1>PT. Joenoes Ikamulya</h1>
        <p>Jl. Pulogadung No.43, RW.9, Jatinegara, Kec. Cakung</p>
        <p>Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13930</p>
        {{-- <p>Telp: (021) 12345678 | Email: info@contohjaya.com</p> --}}
    </div>

    <hr>

    <div class="text-start">
        <h3>Laporan History Asset Tools : {{ $assetTools->name }}</h3>
    </div>

     <!-- Menampilkan periode yang dipilih -->
     <div style="text-align: right; margin-top: 20px;">
        <strong>Periode: </strong>
        @if ($startDate && $endDate)
            {{ $startDate }} s/d {{ $endDate }}
        @elseif ($startDate)
            {{ $startDate }} s/d Hari Ini
        @elseif ($endDate)
            Dari Awal s/d {{ $endDate }}
        @else
            -
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalStock = 0; @endphp
            @foreach ($transactions as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->user ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">{{ $item->created_at->format('d-m-Y') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->type == 'in' ? 'bg-success' : 'bg-danger' }}">
                            {{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                </tr>
                @php
                    // Menghitung stok akhir berdasarkan transaksi masuk (in) dan keluar (out)
                    $totalStock += $item->type == 'in' ? $item->quantity : -$item->quantity;
                @endphp
            @endforeach
            <!-- Menambahkan baris untuk jumlah akhir stok -->
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="2" class="text-center"><strong>Jumlah Akhir Stok</strong></td>
                <td class="text-center"><strong>{{ $totalStock }}</strong></td>
                <!-- User and Tanggal columns are now hidden by colspan -->
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="left">
            <p>Dicetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        </div>
        <div class="right">
            <p>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <br><br><br>
            <p style="text-decoration: underline;">(___________________)</p>
        </div>
    </div>

</body>

</html>
