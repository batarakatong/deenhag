@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Produk Percetakan</h1>
<form class="mb-6 flex flex-wrap gap-3">
    <input class="rounded border p-3" name="search" placeholder="Cari produk" value="{{ request('search') }}">
    <select class="rounded border p-3" name="category">
        <option value="">Semua kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>
        @endforeach
    </select>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<div class="grid gap-5 md:grid-cols-3">
    @foreach($products as $product)
        @include('store.product-card', ['product' => $product])
    @endforeach
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
