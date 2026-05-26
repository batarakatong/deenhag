@extends('layouts.admin')
@section('content')
<div class="mb-5 flex justify-between"><h1 class="text-2xl font-bold">Pembayaran</h1><a class="rounded border px-5 py-3" href="{{ route('admin.exports.payments') }}">Export CSV</a></div>
<form class="mb-5 grid gap-3 rounded bg-white p-4 shadow md:grid-cols-4">
    <input class="rounded border p-3 md:col-span-2" name="search" value="{{ request('search') }}" placeholder="Cari payment/order">
    <select class="rounded border p-3" name="status"><option value="">Semua status</option>@foreach(['unpaid','waiting_confirmation','paid','rejected','refund'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Filter</button>
</form>
<div class="rounded bg-white shadow">
    @foreach($payments as $payment)
        <div class="grid gap-3 border-b p-4 md:grid-cols-6">
            <div class="font-semibold">{{ $payment->payment_number }}</div>
            <div>{{ $payment->order->order_number }}</div>
            <div>{{ $payment->order->user->name }}</div>
            <div>Rp {{ number_format($payment->amount,0,',','.') }}</div>
            <div>@include('partials.status', ['status' => $payment->status])</div>
            <a class="text-green-700" href="{{ route('admin.orders.show',$payment->order) }}">Detail</a>
        </div>
    @endforeach
</div>
<div class="mt-5">{{ $payments->links() }}</div>
@endsection
