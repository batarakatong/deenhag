<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Order {{ $order->order_number }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#111827;margin:24px;font-size:13px}
        .header{display:flex;justify-content:space-between;border-bottom:3px solid #16a34a;padding-bottom:14px;margin-bottom:18px}
        h1,h2,h3{margin:0 0 8px}
        table{width:100%;border-collapse:collapse;margin:12px 0}
        th,td{border:1px solid #d1d5db;padding:8px;vertical-align:top}
        th{background:#f1f5f9;text-align:left}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .box{border:1px solid #d1d5db;border-radius:8px;padding:12px;margin-bottom:12px}
        .check{width:18px;height:18px;border:2px solid #111827;display:inline-block;vertical-align:middle;margin-right:8px}
        .muted{color:#64748b}
        .signature{height:72px}
        @media print{button{display:none} body{margin:12px}}
    </style>
</head>
<body>
    <button onclick="window.print()" style="float:right;padding:10px 16px;border:1px solid #16a34a;background:#16a34a;color:white;border-radius:8px">Print SO</button>
    <div class="header">
        <div>
            <h1>SERVICE ORDER</h1>
            <div class="muted">{{ $appSettings->get('company_name', 'GreenPrinting') }}</div>
            <div class="muted">{{ $appSettings->get('company_phone') }} | {{ $appSettings->get('company_email') }}</div>
        </div>
        <div>
            <h2>{{ $order->order_number }}</h2>
            <div>Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
            <div>Status: {{ $order->status }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <h3>Customer</h3>
            <div>Nama: <b>{{ $order->user->name }}</b></div>
            <div>HP: {{ $order->user->phone ?: '-' }}</div>
            <div>Email: {{ $order->user->email }}</div>
            <div>Alamat: {{ $order->shipment->address ?? $order->customer?->address ?? '-' }}</div>
        </div>
        <div class="box">
            <h3>Instruksi Produksi</h3>
            <div><span class="check"></span> File desain sudah dicek</div>
            <div><span class="check"></span> Ukuran sesuai order</div>
            <div><span class="check"></span> Bahan sesuai spesifikasi</div>
            <div><span class="check"></span> Warna/proof disetujui</div>
            <div><span class="check"></span> Finishing sesuai order</div>
            <div><span class="check"></span> QC akhir selesai</div>
        </div>
    </div>

    <h3>Detail Item Pesanan</h3>
    <table>
        <thead>
            <tr>
                <th style="width:32px">✓</th>
                <th>Produk</th>
                <th>Spesifikasi / Checklist Instrumen</th>
                <th>Qty</th>
                <th>Catatan Produksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td><span class="check"></span></td>
                    <td>
                        <b>{{ $item->product_name }}</b><br>
                        <span class="muted">{{ $item->product?->service_type }} / {{ $item->product?->print_method }}</span>
                    </td>
                    <td>
                        @php($specifications = collect($item->specifications))
                        @if($specifications->get('variant'))
                            <div><span class="check"></span> Varian: {{ $specifications->get('variant') }}</div>
                        @endif
                        @foreach(collect($specifications->get('variants')) as $variantRow)
                            <div><span class="check"></span> {{ $variantRow['variant'] ?? '-' }}: {{ $variantRow['quantity'] ?? 0 }} pcs</div>
                        @endforeach
                        @foreach(collect($specifications->get('options')) as $option)
                            <div><span class="check"></span> Opsi: {{ $option }}</div>
                        @endforeach
                        <div><span class="check"></span> Lebar: {{ $item->width ?: '-' }}</div>
                        <div><span class="check"></span> Tinggi: {{ $item->height ?: '-' }}</div>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>File Terkait</h3>
    <table>
        <thead><tr><th>Checklist</th><th>Nama File</th><th>Tipe</th><th>Catatan</th></tr></thead>
        <tbody>
            @forelse($order->files as $file)
                <tr><td><span class="check"></span></td><td>{{ $file->original_name }}</td><td>{{ $file->file_type }}</td><td>{{ $file->notes }}</td></tr>
            @empty
                <tr><td colspan="4">Belum ada file.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grid">
        <div class="box signature">
            <b>Operator Produksi</b>
        </div>
        <div class="box signature">
            <b>Quality Control</b>
        </div>
    </div>
</body>
</html>
