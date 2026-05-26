@extends('layouts.admin')
@section('content')
<div class="mb-5 flex justify-between"><h1 class="text-2xl font-bold">Stok Bahan</h1><a class="rounded border px-5 py-3" href="{{ route('admin.exports.materials') }}">Export CSV</a></div>
<form class="mb-5 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-5">
    <input class="rounded border p-3" name="search" value="{{ request('search') }}" placeholder="Cari bahan">
    <select class="rounded border p-3" name="material_category_id"><option value="">Kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('material_category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select>
    <select class="rounded border p-3" name="supplier_id"><option value="">Supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(request('supplier_id')==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select>
    <select class="rounded border p-3" name="stock_status"><option value="">Semua stok</option><option value="low" @selected(request('stock_status')==='low')>Menipis</option></select>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<form method="post" action="{{ route('admin.materials.store') }}" class="mb-6 grid gap-3 rounded bg-white p-5 shadow md:grid-cols-4">@csrf
    <input class="rounded border p-3" name="name" placeholder="Nama bahan" required>
    <select class="rounded border p-3" name="material_category_id">@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
    <select class="rounded border p-3" name="supplier_id"><option value="">Supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select>
    <input class="rounded border p-3" name="unit" placeholder="Satuan" value="m2">
    <input class="rounded border p-3" name="current_stock" type="number" step="0.01" placeholder="Stok">
    <input class="rounded border p-3" name="minimum_stock" type="number" step="0.01" placeholder="Stok minimum">
    <input class="rounded border p-3" name="purchase_price" type="number" placeholder="Harga beli">
    <button class="rounded bg-green-600 px-5 py-3 text-white">Tambah Bahan</button>
</form>
<div class="rounded bg-white shadow">
    @foreach($materials as $material)
        <div class="grid gap-3 border-b p-4 md:grid-cols-6">
            <div class="font-semibold">{{ $material->name }}</div>
            <div>{{ $material->category->name }}</div>
            <div>{{ $material->current_stock }} {{ $material->unit }}</div>
            <div>Min {{ $material->minimum_stock }}</div>
            <div>{{ $material->current_stock <= $material->minimum_stock ? 'Menipis' : 'Aman' }}</div>
            <form method="post" action="{{ route('admin.materials.adjust',$material) }}" class="flex gap-2">@csrf<input class="w-20 rounded border p-2" name="quantity" type="number" step="0.01"><button class="text-green-700">Koreksi</button></form>
        </div>
    @endforeach
</div>
<div class="mt-5">{{ $materials->links() }}</div>
@endsection
