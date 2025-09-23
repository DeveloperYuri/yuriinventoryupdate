<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Sparepart</title>
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

        /* Tabel header info */
        .table-header-info td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
            /* kiri */
            vertical-align: top;
        }

        /* Tabel spare part */
        .table-sparepart th {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            /* header tetap kiri */
            background-color: #f0f0f0;
        }

        .table-sparepart td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            /* isi tabel center */
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
        <img src="https://www.baby-dee.co.id/assets/img/logobaru.png" alt="Logo Perusahaan">

        <h1>PT. Joenoes Ikamulya</h1>
        <p>Jl. Pulogadung No.43, RW.9, Jatinegara, Kec. Cakung</p>
        <p>Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13930</p>
        {{-- <p>Telp: (021) 12345678 | Email: info@contohjaya.com</p> --}}
    </div>

    <hr>

    <h2 style="text-align: center;">Surat Pesanan</h2>

    <table class="table-header-info" style="width:100%; border-collapse: collapse; margin-bottom: 20px; border: 0;">
        <tr>
            <td style="width:48%; vertical-align: top">
                <table style="width:100%; border:0; border-collapse: collapse;">
                    <tr>
                        <td style="width:70px; text-align:left; border:0;"><strong>Nomer</strong></td>
                        <td style="width:5px; border:0;">:</td>
                        <td style="border:0;">{{ $transaction->no_surat_pesanan }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left; border:0;"><strong>Di buat oleh</strong></td>
                        <td style="border:0;">:</td>
                        <td style="border:0;">{{ $transaction->name }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left; border:0;"><strong>Lokasi</strong></td>
                        <td style="border:0;">:</td>
                        <td style="border:0;">{{ $transaction->location->name ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:48%; vertical-align: top">
                <table style="width:100%; border:0; border-collapse: collapse;">
                    <tr>
                        <td style="width:80px; text-align:left; border:0;"><strong>Tanggal</strong></td>
                        <td style="width:5px; border:0;">:</td>
                        <td style="border:0;">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left; border:0;"><strong>Category</strong></td>
                        <td style="border:0;">:</td>
                        <td style="border:0;">{{ $transaction->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left; border:0;"><strong>Sub Category</strong></td>
                        <td style="border:0;">:</td>
                        <td style="border:0;">{{ $transaction->subcategory->name ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <table class="table-sparepart">
        <thead>
            <tr>
                <th>Nama Spare Part</th>
                <th>Qty</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->details as $detail)
                <tr>
                    <td>{{ $detail->sparePart->name ?? '-' }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->stock }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="left">
            <p style="text-align: center">Pemohon</p>
            <br><br>
            <p style="text-decoration: underline;text-align: center">( ____________ )</p>
        </div>
        <div class="right">
            <p style="text-align: center">Di setujui</p>
            <br><br><br>
            <p style="text-decoration: underline;text-align: center">( ____________ )</p>
            {{-- <p>Penanggung Jawab Gudang</p> --}}
        </div>
    </div>

</body>

</html>
