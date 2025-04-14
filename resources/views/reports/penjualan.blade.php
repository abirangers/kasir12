<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>Semua Transaksi</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if(count($penjualan) > 0)
                @foreach($penjualan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ date('d-m-Y H:i', strtotime($item->tanggal_penjualan)) }}</td>
                        <td>{{ $item->pelanggan->nama }}</td>
                        <td>{{ $item->kasir }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data penjualan</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Pendapatan</td>
                <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak oleh sistem kasir.</p>
    </div>
</body>
</html> 