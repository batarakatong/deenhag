@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Produk Percetakan</h1>
<form class="mb-6 grid gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:grid-cols-3">
    <input class="rounded-xl border p-3" name="search" placeholder="Cari produk" value="{{ request('search') }}">
    <select class="rounded-xl border p-3" name="category">
        <option value="">Semua kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>
        @endforeach
    </select>
    <button class="rounded-xl bg-green-600 px-5 py-3 font-semibold text-white">Filter</button>
</form>
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($products as $product)
        @include('store.product-card', ['product' => $product])
    @endforeach
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
