<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body{font-family: DejaVu Sans, sans-serif;font-size:12px;color:#111827}
        .head{display:flex;justify-content:space-between;border-bottom:2px solid #16a34a;padding-bottom:16px;margin-bottom:20px}
        table{width:100%;border-collapse:collapse;margin-top:16px}
        th,td{border:1px solid #e5e7eb;padding:8px;text-align:left}
        th{background:#dcfce7}
        .right{text-align:right}
    </style>
</head>
<body>
    <div class="head">
        <div>
            <h1>{{ $appSettings->get('company_name', 'GreenPrinting') }}</h1>
            <p>{{ $appSettings->get('company_address', 'Alamat toko') }}</p>
            <p>{{ $appSettings->get('company_phone') }} | {{ $appSettings->get('company_email') }}</p>
        </div>
        <div><h2>{{ $order->invoice->invoice_number }}</h2><p>{{ $order->invoice->invoice_date->format('d M Y') }}</p></div>
    </div>
    <p><b>Customer:</b> {{ $order->user->name }}<br><b>Order:</b> {{ $order->order_number }}<br><b>Status:</b> {{ $order->payment_status }}</p>
    <table>
        <thead><tr><th>Produk</th><th>Spesifikasi</th><th>Qty</th><th class="right">Total</th></tr></thead>
        <tbody>
            @foreach($order->items as $item)
                <tr><td>{{ $item->product_name }}</td><td>{{ collect($item->specifications)->flatten()->join(', ') }}</td><td>{{ $item->quantity }}</td><td class="right">Rp {{ number_format($item->total_price,0,',','.') }}</td></tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="right">Subtotal</td><td class="right">Rp {{ number_format($order->subtotal,0,',','.') }}</td></tr>
            <tr><td colspan="3" class="right">Ongkir</td><td class="right">Rp {{ number_format($order->shipping_cost,0,',','.') }}</td></tr>
            <tr><td colspan="3" class="right"><b>Total</b></td><td class="right"><b>Rp {{ number_format($order->grand_total,0,',','.') }}</b></td></tr>
        </tfoot>
    </table>
    <p>Instruksi pembayaran: Transfer ke rekening toko sesuai instruksi admin.</p>
    <p>{{ $appSettings->get('footer_text', 'Terima kasih sudah mempercayakan kebutuhan cetak Anda kepada GreenPrinting.') }}</p>
</body>
</html>
