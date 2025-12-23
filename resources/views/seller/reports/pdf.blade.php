<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #2563eb; }
        .meta { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .total { font-weight: bold; background-color: #e0f2fe; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>SquareOne - Laporan Penjualan</h2>
        <p>Toko: {{ $seller_name }}</p>
    </div>

    <div class="meta">
        <p><strong>Periode:</strong> {{ $start_date }} s/d {{ $end_date }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Item</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>#{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->buyer->name }}</td>
                <td>
                    @foreach($order->items as $item)
                        <div>- {{ $item->product->name }} (x{{ $item->quantity }})</div>
                    @endforeach
                </td>
                <td style="text-align: right;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data penjualan pada periode ini.</td>
            </tr>
            @endforelse
            
            <tr class="total">
                <td colspan="5" style="text-align: right;">TOTAL OMZET</td>
                <td style="text-align: right;">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem SquareOne
    </div>

</body>
</html>