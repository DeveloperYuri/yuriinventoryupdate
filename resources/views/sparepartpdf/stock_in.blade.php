<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Masuk</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; }
        hr { border: 1px solid black; margin: 10px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }

        .footer { margin-top: 30px; width: 100%; }
        .footer .left { float: left; }
        .footer .right { float: right; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('assets/img/logobaru.png') }}" 
         alt="Logo Perusahaan" 
         style="width:80px; height:auto; display:block; margin:0 auto 10px auto;">
        
        <h1>PT. Joenoes Ikamulya</h1>
        <p>Jl. Pulogadung No.43, RW.9, Jatinegara, Kec. Cakung</p>
        <p>Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13930</p>
        {{-- <p>Telp: (021) 12345678 | Email: info@contohjaya.com</p> --}}
    </div>

    <hr>

    <h3 style="text-align: center;">Laporan Stok Masuk Spare Part</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Penerima</th>
                <th>Jumlah Masuk</th>
                <th>Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockIns as $no => $item)
            <tr>
                <td>{{ $no+1 }}</td>
                <td>{{ $item->sparePart->name }}</td>
                <td>{{ $item->user }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="left">
            <p>Dicetak: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
        </div>
        <div class="right">
            <p>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <br><br><br>
            <p style="text-decoration: underline;">( ____________ )</p>
        </div>
    </div>

</body>
</html>
