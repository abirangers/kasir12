<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Penjualan #{{ $penjualan->id }}</title>
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
        .info {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
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
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            margin: 5px 0;
        }
        .total-label {
            font-weight: bold;
            margin-right: 20px;
        }
        .total-value {
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Penjualan #{{ $penjualan->id }}</h1>
        <p>Tanggal: {{ date('d-m-Y H:i', strtotime($penjualan->tanggal_penjualan)) }}</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span class="info-label">Pelanggan:</span>
            <span>{{ $penjualan->pelanggan->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Alamat:</span>
            <span>{{ $penjualan->pelanggan->alamat }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">No. Telepon:</span>
            <span>{{ $penjualan->pelanggan->no_hp }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kasir:</span>
            <span>{{ $penjualan->user->name ?? 'Admin' }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detailPenjualan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->produk->nama }}</td>
                    <td class="text-right">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->jumlah_produk }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Total:</span>
            <span class="total-value">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Terimakasih telah berbelanja di toko kami.</p>
    </div>
</body>
</html> 