@extends('layouts.admin')
@section('content')
<div class="mb-5 flex justify-between"><h1 class="text-2xl font-bold">Pesanan</h1><a class="rounded border px-5 py-3" href="{{ route('admin.exports.orders') }}">Export CSV</a></div>
<form class="mb-5 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-6">
    <input class="rounded border p-3 md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari order/customer">
    <select class="rounded border p-3" name="status"><option value="">Status order</option>@foreach(['pending_payment','payment_confirmed','waiting_design','file_received','design_process','waiting_approval','printing','finishing','ready_pickup','shipped','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
    <select class="rounded border p-3" name="payment_status"><option value="">Status bayar</option>@foreach(['unpaid','waiting_confirmation','paid','rejected','refund'] as $status)<option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ $status }}</option>@endforeach</select>
    <input class="rounded border p-3" type="date" name="from" value="{{ request('from') }}">
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<div class="rounded bg-white shadow">
    @foreach($orders as $order)
        <a class="grid gap-3 border-b p-4 md:grid-cols-6" href="{{ route('admin.orders.show',$order) }}">
            <div class="font-semibold md:col-span-2">{{ $order->order_number }}</div>
            <div>{{ $order->user->name }}</div>
            <div>Rp {{ number_format($order->grand_total,0,',','.') }}</div>
            <div>@include('partials.status', ['status' => $order->payment_status])</div>
            <div>@include('partials.status', ['status' => $order->status])</div>
        </a>
    @endforeach
</div>
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
