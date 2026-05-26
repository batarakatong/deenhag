@extends('layouts.admin')
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold">Produksi</h1>
        <p class="text-sm text-slate-600">Antrian kerja produksi. Staff bisa memindahkan pesanan ke tahap produksi berikutnya.</p>
    </div>
    <a class="rounded border px-5 py-3" href="{{ route('admin.production.steps') }}">Atur Alur Produksi</a>
</div>
<div class="grid gap-4 overflow-x-auto lg:grid-cols-4 xl:grid-cols-6">
    @foreach($steps as $step)
        <section class="min-w-64 rounded bg-white p-4 shadow">
            <div class="mb-4">
                <h2 class="font-bold">{{ $step->name }}</h2>
                <p class="text-xs text-slate-500">{{ $step->description }}</p>
            </div>
            <div class="grid gap-3">
                @foreach($orders->where('status', $step->status_key) as $order)
                    <div class="rounded border p-3">
                        <div class="font-semibold">{{ $order->order_number }}</div>
                        <div class="text-sm text-slate-600">{{ $order->user->name }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $order->items->pluck('product_name')->join(', ') }}</div>
                        <form method="post" action="{{ route('admin.production.orders.update', $order) }}" class="mt-3">@csrf @method('patch')
                            <select class="mb-2 w-full rounded border p-2 text-sm" name="status">
                                @foreach($steps as $target)
                                    <option value="{{ $target->status_key }}" @selected($order->status===$target->status_key)>{{ $target->name }}</option>
                                @endforeach
                            </select>
                            <button class="w-full rounded bg-green-600 px-3 py-2 text-sm text-white">Update</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
@endsection
