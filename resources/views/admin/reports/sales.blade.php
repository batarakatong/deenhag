@extends('layouts.admin')
@section('content')
<style>
    @media print {
        aside, header, footer, .no-print { display:none!important; }
        main { padding:0!important; }
        body { background:white!important; }
        .print-panel { box-shadow:none!important; border:1px solid #ddd; }
    }
</style>
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">Laporan Penjualan</h1>
    <button onclick="window.print()" class="btn btn-secondary no-print">Cetak Laporan</button>
</div>
<form class="no-print mb-5 grid gap-3 rounded-xl bg-white p-4 shadow md:grid-cols-7">
    <input class="field md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari order/customer/email">
    <input class="field" type="date" name="from" value="{{ request('from') }}">
    <input class="field" type="date" name="to" value="{{ request('to') }}">
    <select class="field" name="payment_status">
        <option value="">Status bayar</option>
        @foreach(['unpaid','waiting_confirmation','paid','rejected','refund'] as $status)
            <option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <select class="field" name="status">
        <option value="">Status order</option>
        @foreach(['pending_payment','payment_confirmed','waiting_design','file_received','design_process','waiting_approval','printing','sablon_process','finishing','ready_pickup','shipped','completed','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filter</button>
</form>
<div class="mb-5 grid gap-4 md:grid-cols-4">
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Total Order</div><div class="text-2xl font-bold">{{ $orders->count() }}</div></div>
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Omzet</div><div class="text-2xl font-bold">Rp {{ number_format($orders->sum('grand_total'),0,',','.') }}</div></div>
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Dibayar</div><div class="text-2xl font-bold">Rp {{ number_format($orders->where('payment_status','paid')->sum('grand_total'),0,',','.') }}</div></div>
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Belum Dibayar</div><div class="text-2xl font-bold">Rp {{ number_format($orders->where('payment_status','!=','paid')->sum('grand_total'),0,',','.') }}</div></div>
</div>
<div class="print-panel rounded-xl bg-white shadow">
    <div class="grid gap-3 border-b bg-slate-50 p-4 font-bold md:grid-cols-6">
        <div>Tanggal</div><div>Invoice/Order</div><div>Customer</div><div>Total</div><div>Bayar</div><div>Status</div>
    </div>
    @forelse($orders as $order)
        <div class="grid gap-3 border-b p-4 md:grid-cols-6">
            <div>{{ $order->created_at->format('d M Y') }}</div>
            <div>{{ $order->order_number }}</div>
            <div>{{ $order->user->name }}</div>
            <div>Rp {{ number_format($order->grand_total,0,',','.') }}</div>
            <div>{{ $order->payment_status }}</div>
            <div>{{ $order->status }}</div>
        </div>
    @empty
        <div class="p-5 text-slate-500">Tidak ada data penjualan.</div>
    @endforelse
</div>
@endsection
