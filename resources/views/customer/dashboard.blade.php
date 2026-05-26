@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Dashboard Customer</h1>
@if(!auth()->user()->phone)
    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900">
        Lengkapi nomor HP/WhatsApp agar Anda menerima notifikasi saat status pesanan berubah.
        <a class="font-semibold text-green-700" href="{{ route('customer.profile.edit') }}">Lengkapi akun</a>
    </div>
@endif
<div class="mb-6 grid gap-4 md:grid-cols-4">
    <div class="rounded bg-white p-5 shadow"><div class="text-sm text-slate-500">Total Pesanan</div><div class="text-2xl font-bold">{{ $orders->count() }}</div></div>
    <div class="rounded bg-white p-5 shadow"><div class="text-sm text-slate-500">Menunggu Bayar</div><div class="text-2xl font-bold">{{ $orders->where('payment_status','unpaid')->count() }}</div></div>
    <div class="rounded bg-white p-5 shadow"><div class="text-sm text-slate-500">Proses</div><div class="text-2xl font-bold">{{ $orders->whereNotIn('status',['completed','cancelled'])->count() }}</div></div>
    <div class="rounded bg-white p-5 shadow"><div class="text-sm text-slate-500">Selesai</div><div class="text-2xl font-bold">{{ $orders->where('status','completed')->count() }}</div></div>
</div>
@include('customer.order-list', ['orders' => $orders])
@endsection
