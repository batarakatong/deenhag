@extends('layouts.app')
@section('content')
<section class="grid gap-8 rounded bg-white p-8 shadow md:grid-cols-2 md:items-center">
    <div>
        <p class="mb-2 font-semibold text-green-700">Percetakan online untuk bisnis</p>
        <h1 class="text-4xl font-bold leading-tight">Cetak banner, brosur, kartu nama, stiker, dan kebutuhan promosi lebih mudah.</h1>
        <p class="mt-4 text-slate-600">Pilih produk, hitung harga otomatis, upload desain, checkout, lalu pantau status produksi dari akun Anda.</p>
        <div class="mt-6 flex gap-3">
            <a class="rounded bg-green-600 px-5 py-3 font-semibold text-white" href="{{ route('products.index') }}">Lihat Produk</a>
            <a class="rounded border px-5 py-3 font-semibold" href="{{ route('track') }}">Cek Pesanan</a>
        </div>
    </div>
    <div class="rounded bg-green-50 p-8">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="rounded bg-white p-4 shadow">Harga transparan</div>
            <div class="rounded bg-white p-4 shadow">Upload desain</div>
            <div class="rounded bg-white p-4 shadow">Manual transfer</div>
            <div class="rounded bg-white p-4 shadow">Tracking order</div>
        </div>
    </div>
</section>
<h2 class="mt-10 mb-4 text-2xl font-bold">Kategori</h2>
<div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
    @foreach($categories as $category)
        <a class="rounded bg-white p-4 shadow" href="{{ route('products.index', ['category' => $category->slug]) }}">
            <div class="font-semibold">{{ $category->name }}</div>
            <div class="text-sm text-slate-500">{{ $category->products_count }} produk</div>
        </a>
    @endforeach
</div>
<h2 class="mt-10 mb-4 text-2xl font-bold">Produk Populer</h2>
<div class="grid gap-5 md:grid-cols-3">
    @foreach($products as $product)
        @include('store.product-card', ['product' => $product])
    @endforeach
</div>
@endsection
