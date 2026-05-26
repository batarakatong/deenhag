@extends('layouts.admin')
@section('content')
<style>
    @keyframes rainbowPulse {
        0% { background:#dcfce7; border-color:#22c55e; box-shadow:0 0 0 rgba(34,197,94,.2); }
        25% { background:#dbeafe; border-color:#3b82f6; box-shadow:0 0 18px rgba(59,130,246,.35); }
        50% { background:#fef3c7; border-color:#f59e0b; box-shadow:0 0 18px rgba(245,158,11,.35); }
        75% { background:#fce7f3; border-color:#ec4899; box-shadow:0 0 18px rgba(236,72,153,.35); }
        100% { background:#dcfce7; border-color:#22c55e; box-shadow:0 0 0 rgba(34,197,94,.2); }
    }
    .production-blink { animation: rainbowPulse 1.5s infinite; }
</style>

<form class="mb-6 grid gap-3 rounded-xl bg-white p-4 shadow md:grid-cols-6">
    <select class="field" name="month">
        @foreach(range(1,12) as $m)
            <option value="{{ $m }}" @selected($filters['month']===$m)>{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
        @endforeach
    </select>
    <select class="field" name="year">
        @foreach(range(now()->year - 3, now()->year + 1) as $y)
            <option value="{{ $y }}" @selected($filters['year']===$y)>{{ $y }}</option>
        @endforeach
    </select>
    <input class="field" type="date" name="date_from" value="{{ request('date_from') }}">
    <input class="field" type="date" name="date_to" value="{{ request('date_to') }}">
    <button class="btn btn-primary md:col-span-2">Terapkan Filter Dashboard</button>
</form>

<div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
    @foreach([['Pesanan Hari Ini',$ordersToday],['Penjualan Bulan Ini','Rp '.number_format($salesMonth,0,',','.')],['Pending Bayar',$pendingPayments],['Produksi',$inProduction],['Selesai',$completed],['Stok Menipis',$lowStocks->count()]] as $card)
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="text-sm text-slate-500">{{ $card[0] }}</div>
            <div class="mt-2 text-2xl font-bold">{{ $card[1] }}</div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-xl bg-white p-5 shadow xl:col-span-2">
        <div class="mb-4 flex flex-wrap justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Chart Penjualan</h2>
                <p class="text-sm text-slate-500">Periode {{ $filters['from']->format('d M Y') }} - {{ $filters['to']->format('d M Y') }}</p>
            </div>
            <div class="font-bold text-green-700">Rp {{ number_format($paidSales,0,',','.') }}</div>
        </div>
        <canvas id="salesChart" height="130"></canvas>
    </section>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">Laba Rugi</h2>
        <canvas id="profitChart" height="160"></canvas>
        <div class="mt-4 grid gap-2 text-sm">
            <div class="flex justify-between"><span>Penjualan dibayar</span><b>Rp {{ number_format($paidSales,0,',','.') }}</b></div>
            <div class="flex justify-between"><span>Pembelian bahan</span><b>Rp {{ number_format($purchaseCost,0,',','.') }}</b></div>
            <div class="flex justify-between"><span>Estimasi bahan terpakai</span><b>Rp {{ number_format($estimatedMaterialCost,0,',','.') }}</b></div>
            <div class="flex justify-between border-t pt-2 text-base"><span>Laba bersih estimasi</span><b class="{{ $profit >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($profit,0,',','.') }}</b></div>
        </div>
    </section>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="rounded-xl bg-white p-5 shadow xl:col-span-2">
        <h2 class="mb-4 text-lg font-bold">Produksi Berjalan</h2>
        <div class="grid gap-3 md:grid-cols-2">
            @forelse($productionOrders as $order)
                <a class="production-blink rounded-xl border p-4" href="{{ route('admin.orders.show', $order) }}">
                    <div class="font-bold">{{ $order->order_number }}</div>
                    <div class="text-sm text-slate-700">{{ $order->user->name }}</div>
                    <div class="mt-2 inline-flex rounded-full bg-white/70 px-3 py-1 text-xs font-bold text-slate-800">{{ $order->status }}</div>
                </a>
            @empty
                <div class="rounded border p-4 text-slate-500">Belum ada produksi berjalan.</div>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">Grafik Visitor</h2>
        <canvas id="visitorChart" height="170"></canvas>
        <p class="mt-3 text-xs text-slate-500">Saat ini memakai gabungan aktivitas order, WAHA, dan estimasi kunjungan lokal. Bisa diganti analytics real saat deployment.</p>
    </section>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">10 Pelanggan Terbaik</h2>
        <div class="grid gap-3">
            @forelse($topCustomers as $index => $customer)
                <div class="flex items-center justify-between rounded-lg border p-3">
                    <div><b>#{{ $index + 1 }} {{ $customer->user?->name }}</b><div class="text-xs text-slate-500">{{ $customer->orders_count }} pesanan</div></div>
                    <div class="font-bold text-green-700">Rp {{ number_format($customer->total,0,',','.') }}</div>
                </div>
            @empty
                <div class="text-slate-500">Belum ada data pelanggan.</div>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-bold">5 Barang Paling Laris</h2>
        <div class="grid gap-3">
            @forelse($bestProducts as $index => $product)
                <div class="rounded-lg border p-3">
                    <div class="flex justify-between"><b>#{{ $index + 1 }} {{ $product->product_name }}</b><span>{{ $product->qty }} item</span></div>
                    <div class="mt-2 h-2 rounded bg-slate-100"><div class="h-2 rounded bg-green-500" style="width: {{ min(100, $product->qty * 10) }}%"></div></div>
                    <div class="mt-1 text-xs text-slate-500">Rp {{ number_format($product->total,0,',','.') }}</div>
                </div>
            @empty
                <div class="text-slate-500">Belum ada produk terjual.</div>
            @endforelse
        </div>
    </section>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 font-bold">Pesanan Terbaru</h2>
        @foreach($recentOrders as $order)
            <a class="block border-b py-3 last:border-0" href="{{ route('admin.orders.show', $order) }}">
                <div class="font-semibold">{{ $order->order_number }} - {{ $order->user->name }}</div>
                <div class="text-sm text-slate-500">Rp {{ number_format($order->grand_total,0,',','.') }} | {{ $order->status }}</div>
            </a>
        @endforeach
    </section>
    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 font-bold">Stok Hampir Habis</h2>
        @forelse($lowStocks as $material)
            <div class="border-b py-3 last:border-0">{{ $material->name }}: {{ $material->current_stock }} {{ $material->unit }}</div>
        @empty
            <div class="text-slate-500">Tidak ada stok menipis.</div>
        @endforelse
    </section>
</div>

<script>
const money = value => new Intl.NumberFormat('id-ID').format(value);
function drawLineChart(canvasId, labels, values, color = '#16a34a') {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
    const h = canvas.height = canvas.offsetHeight * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    const width = canvas.offsetWidth, height = canvas.offsetHeight;
    ctx.clearRect(0,0,width,height);
    const pad = 34, max = Math.max(...values, 1);
    ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 1;
    for (let i=0;i<4;i++){ const y = pad + i*(height-pad*2)/3; ctx.beginPath(); ctx.moveTo(pad,y); ctx.lineTo(width-pad,y); ctx.stroke(); }
    ctx.strokeStyle = color; ctx.lineWidth = 3; ctx.beginPath();
    values.forEach((v,i)=>{ const x = pad + i*(width-pad*2)/Math.max(values.length-1,1); const y = height-pad - (v/max)*(height-pad*2); i?ctx.lineTo(x,y):ctx.moveTo(x,y); });
    ctx.stroke();
    values.forEach((v,i)=>{ const x = pad + i*(width-pad*2)/Math.max(values.length-1,1); const y = height-pad - (v/max)*(height-pad*2); ctx.fillStyle=color; ctx.beginPath(); ctx.arc(x,y,4,0,Math.PI*2); ctx.fill(); });
    ctx.fillStyle = '#64748b'; ctx.font = '11px sans-serif';
    labels.forEach((label,i)=>{ if(i % Math.ceil(labels.length/6)===0){ const x = pad + i*(width-pad*2)/Math.max(labels.length-1,1); ctx.fillText(label,x-12,height-8); }});
}
function drawBarChart(canvasId, labels, values, colors) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = canvas.offsetWidth * window.devicePixelRatio; canvas.height = canvas.offsetHeight * window.devicePixelRatio; ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    const width = canvas.offsetWidth, height = canvas.offsetHeight, pad = 28, max = Math.max(...values, 1);
    const barW = (width - pad*2) / values.length * .62;
    values.forEach((v,i)=>{ const x = pad + i*(width-pad*2)/values.length + barW*.3; const barH = (v/max)*(height-pad*2); ctx.fillStyle = colors[i % colors.length]; ctx.fillRect(x,height-pad-barH,barW,barH); ctx.fillStyle='#64748b'; ctx.font='11px sans-serif'; ctx.fillText(labels[i],x,height-8); });
}
drawLineChart('salesChart', @json($salesChart->pluck('label')), @json($salesChart->pluck('value')), '#16a34a');
drawBarChart('profitChart', ['Sales','Cost','Profit'], [{{ $paidSales }}, {{ $purchaseCost + $estimatedMaterialCost }}, {{ max($profit, 0) }}], ['#16a34a','#f59e0b','#2563eb']);
drawLineChart('visitorChart', @json($visitorReport->pluck('label')), @json($visitorReport->pluck('value')), '#7c3aed');
</script>
@endsection
