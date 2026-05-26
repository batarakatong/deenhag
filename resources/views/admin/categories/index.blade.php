@extends('layouts.admin')
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">Kategori Produk</h1>
    <a class="rounded border px-5 py-3" href="{{ route('admin.exports.categories') }}">Export CSV</a>
</div>
<form class="mb-5 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-4">
    <input class="rounded border p-3 md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari kategori">
    <select class="rounded border p-3" name="is_active">
        <option value="">Semua status</option>
        <option value="1" @selected(request('is_active')==='1')>Aktif</option>
        <option value="0" @selected(request('is_active')==='0')>Nonaktif</option>
    </select>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<form method="post" action="{{ route('admin.categories.store') }}" class="mb-6 grid gap-3 rounded bg-white p-5 shadow md:grid-cols-5">@csrf
    <input class="rounded border p-3" name="name" placeholder="Nama kategori" required>
    <input class="rounded border p-3 md:col-span-2" name="description" placeholder="Deskripsi">
    <input class="rounded border p-3" name="sort_order" type="number" value="0" placeholder="Urutan">
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
    <button class="rounded bg-green-600 px-5 py-3 text-white md:col-span-5">Tambah Kategori</button>
</form>
<div class="grid gap-4">
    @foreach($categories as $category)
        <form method="post" action="{{ route('admin.categories.update', $category) }}" class="grid gap-3 rounded bg-white p-4 shadow md:grid-cols-7">@csrf @method('patch')
            <input class="rounded border p-3 font-semibold" name="name" value="{{ $category->name }}">
            <input class="rounded border p-3 md:col-span-2" name="description" value="{{ $category->description }}">
            <input class="rounded border p-3" name="sort_order" type="number" value="{{ $category->sort_order }}">
            <div class="text-sm text-slate-500">{{ $category->products_count }} produk</div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Aktif</label>
            <div class="flex gap-3">
                <button class="text-green-700">Update</button>
                <button form="delete-category-{{ $category->id }}" class="text-red-600" type="submit">Delete</button>
            </div>
        </form>
        <form id="delete-category-{{ $category->id }}" method="post" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('delete')</form>
    @endforeach
</div>
<div class="mt-5">{{ $categories->links() }}</div>
@endsection
