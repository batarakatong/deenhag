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
    <h1 class="text-2xl font-bold">Laporan Stok</h1>
    <button onclick="window.print()" class="btn btn-secondary no-print">Cetak Laporan</button>
</div>
<form class="no-print mb-5 grid gap-3 rounded-xl bg-white p-4 shadow md:grid-cols-7">
    <input class="field md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari bahan">
    <select class="field" name="material_category_id">
        <option value="">Kategori bahan</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('material_category_id')==$category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <select class="field" name="stock_status">
        <option value="">Semua stok</option>
        <option value="low" @selected(request('stock_status')==='low')>Stok menipis</option>
    </select>
    <select class="field" name="movement_type">
        <option value="">Semua movement</option>
        @foreach(['in','out','adjustment'] as $type)
            <option value="{{ $type }}" @selected(request('movement_type')===$type)>{{ $type }}</option>
        @endforeach
    </select>
    <input class="field" type="date" name="from" value="{{ request('from') }}">
    <button class="btn btn-primary">Filter</button>
</form>
<div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Total Bahan</div><div class="text-2xl font-bold">{{ $materials->count() }}</div></div>
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Stok Menipis</div><div class="text-2xl font-bold">{{ $materials->filter(fn($m) => $m->current_stock <= $m->minimum_stock)->count() }}</div></div>
    <div class="print-panel rounded-xl bg-white p-5 shadow"><div class="text-sm text-slate-500">Movement Ditampilkan</div><div class="text-2xl font-bold">{{ $movements->count() }}</div></div>
</div>
<div class="print-panel mb-6 rounded-xl bg-white shadow">
    <div class="grid gap-3 border-b bg-slate-50 p-4 font-bold md:grid-cols-5"><div>Bahan</div><div>Kategori</div><div>Stok</div><div>Minimum</div><div>Status</div></div>
    @foreach($materials as $material)
        <div class="grid gap-3 border-b p-4 md:grid-cols-5">
            <div class="font-semibold">{{ $material->name }}</div>
            <div>{{ $material->category->name }}</div>
            <div>{{ $material->current_stock }} {{ $material->unit }}</div>
            <div>{{ $material->minimum_stock }}</div>
            <div>{{ $material->current_stock <= $material->minimum_stock ? 'Menipis' : 'Aman' }}</div>
        </div>
    @endforeach
</div>
<h2 class="mb-3 text-xl font-bold">Pergerakan Stok</h2>
<div class="print-panel rounded-xl bg-white shadow">
    <div class="grid gap-3 border-b bg-slate-50 p-4 font-bold md:grid-cols-5"><div>Tanggal</div><div>Bahan</div><div>Tipe</div><div>Qty</div><div>Stok</div></div>
    @forelse($movements as $movement)
        <div class="grid gap-3 border-b p-4 md:grid-cols-5">
            <div>{{ $movement->created_at->format('d M Y H:i') }}</div>
            <div>{{ $movement->material->name }}</div>
            <div>{{ $movement->movement_type }}</div>
            <div>{{ $movement->quantity }}</div>
            <div>{{ $movement->stock_before }} -> {{ $movement->stock_after }}</div>
        </div>
    @empty
        <div class="p-5 text-slate-500">Tidak ada movement stok.</div>
    @endforelse
</div>
@endsection
