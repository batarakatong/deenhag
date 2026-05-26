@extends('layouts.admin')
@section('content')
<div class="mb-5 flex justify-between">
    <h1 class="text-2xl font-bold">Produk Percetakan</h1>
    <div class="flex gap-2">
        <a class="rounded border px-5 py-3" href="{{ route('admin.exports.products') }}">Export CSV</a>
        <a class="rounded bg-green-600 px-5 py-3 text-white" href="{{ route('admin.products.create') }}">Tambah Produk</a>
    </div>
</div>
<form class="mb-5 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-6">
    <input class="rounded border p-3 md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, bahan">
    <select class="rounded border p-3" name="category_id"><option value="">Kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select>
    <select class="rounded border p-3" name="service_type"><option value="">Layanan</option>@foreach(['printing','sablon','design','finishing','merchandise'] as $type)<option value="{{ $type }}" @selected(request('service_type')===$type)>{{ $type }}</option>@endforeach</select>
    <select class="rounded border p-3" name="pricing_type"><option value="">Tipe harga</option>@foreach(['pcs','meter','square_meter','package','rim','manual'] as $type)<option value="{{ $type }}" @selected(request('pricing_type')===$type)>{{ $type }}</option>@endforeach</select>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<div class="rounded bg-white shadow">
    @foreach($products as $product)
        <div class="grid gap-3 border-b p-4 md:grid-cols-9">
            <div class="font-semibold md:col-span-2">{{ $product->name }}<div class="text-xs text-slate-500">{{ $product->product_code ?: '-' }}</div></div>
            <div>{{ $product->category->name }}</div>
            <div>{{ $product->service_type }}</div>
            <div>{{ $product->print_method ?: '-' }}</div>
            <div>Rp {{ number_format($product->base_price,0,',','.') }}</div>
            <div>{{ $product->pricing_type }}</div>
            <div>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</div>
            <div class="flex gap-3"><a class="text-green-700" href="{{ route('admin.products.edit', $product) }}">Edit</a><form method="post" action="{{ route('admin.products.destroy',$product) }}">@csrf @method('delete')<button class="text-red-600">Hapus</button></form></div>
        </div>
    @endforeach
</div>
<div class="mt-5">{{ $products->links() }}</div>
@endsection
