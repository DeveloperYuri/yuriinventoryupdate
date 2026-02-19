<!DOCTYPE html>
<html>

<head>
    {{-- <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style> --}}

    <style>
        /* Ini rahasianya supaya class text-center kamu jalan */
        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: middle;
            /* Supaya teks di tengah secara vertikal juga */
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; width: 100%;">Rekap Pesanan Barang</h2>

    <div class="info-cetak">
        Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>

    <br>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Nama Spare Part</th>
                <th class="text-center">Qty yang harus di beli</th>
                <th class="text-center">No SP</th>
                <th class="text-center">User</th>
                {{-- <th>Keterangan</th> --}}
                <th class="text-center">Tgl Pembelian</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop menggunakan variabel yang dikirim dari Controller --}}
            @php $no = 1; @endphp

            @foreach ($data_barang as $item)
                {{-- Hanya tampilkan baris jika qty_kurang lebih besar dari 0 --}}


                @if ($item->qty_kurang > 0)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td> {{-- Nomor urut otomatis bertambah --}}

                        <td class="text-center">{{ $item->sparePart->name ?? 'N/A' }}</td>

                        <td class="text-center">{{ $item->qty_kurang }}</td>

                        <td class="text-center">{{ $item->header->no_surat_pesanan ?? '-' }}</td>

                        {{-- Mengambil nama pembuat surat dari header --}}
                        <td class="text-center">{{ $item->header->name ?? '-' }}</td>

                        <td></td>
                    </tr>
                @endif
            @endforeach
            {{-- @foreach ($data_barang as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->sparePart->name ?? 'N/A' }}</td>
                    <td>{{ $item->qty_kurang }}</td>
                    <td>{{ $item->header->no_surat_pesanan ?? '-' }}</td>
                    <td>{{ $item->header->user->name ?? ($item->header->name ?? '-') }}</td>
                    <td></td>
                </tr>
            @endforeach --}}
        </tbody>
    </table>
</body>

</html>
