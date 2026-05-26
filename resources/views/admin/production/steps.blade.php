@extends('layouts.admin')
@section('content')
<h1 class="mb-5 text-2xl font-bold">Alur Produksi</h1>
<form method="post" action="{{ route('admin.production.steps.store') }}" class="mb-6 grid gap-3 rounded bg-white p-5 shadow md:grid-cols-4">@csrf
    <input class="rounded border p-3" name="name" placeholder="Nama tahap, contoh Proses Sablon" required>
    <input class="rounded border p-3 md:col-span-2" name="description" placeholder="Deskripsi singkat untuk staff produksi">
    <input class="rounded border p-3" name="sort_order" type="number" value="{{ $steps->max('sort_order') + 1 }}" placeholder="Urutan">
    <button class="rounded bg-green-600 px-5 py-3 text-white md:col-span-4">Tambah Tahap</button>
</form>
<div class="grid gap-4">
    @foreach($steps as $step)
        <form method="post" action="{{ route('admin.production.steps.update', $step) }}" class="grid gap-3 rounded bg-white p-4 shadow md:grid-cols-7">@csrf @method('patch')
            <input class="rounded border p-3 font-semibold" name="name" value="{{ $step->name }}">
            <input class="rounded border p-3 md:col-span-2" name="description" value="{{ $step->description }}">
            <input class="rounded border p-3" name="sort_order" type="number" value="{{ $step->sort_order }}">
            <div class="text-xs text-slate-500">{{ $step->status_key }}</div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($step->is_active)> Aktif</label>
            <div class="flex gap-3">
                <button class="text-green-700">Update</button>
                <button form="delete-step-{{ $step->id }}" class="text-red-600" type="submit">Delete</button>
            </div>
        </form>
        <form id="delete-step-{{ $step->id }}" method="post" action="{{ route('admin.production.steps.destroy', $step) }}">@csrf @method('delete')</form>
    @endforeach
</div>
@endsection
