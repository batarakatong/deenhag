@extends('layouts.admin')
@section('content')
<div class="mb-5 flex justify-between">
    <h1 class="text-2xl font-bold">Customer</h1>
    <a class="btn btn-primary" href="{{ route('admin.customers.create') }}">Tambah Customer</a>
</div>
<form class="mb-5 grid gap-3 rounded-xl bg-white p-4 shadow md:grid-cols-4">
    <input class="field md:col-span-3" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, HP, kode customer">
    <button class="btn btn-primary">Cari</button>
</form>
<div class="grid gap-3">
@foreach($customers as $customer)
    <a class="rounded-xl bg-white p-5 shadow" href="{{ route('admin.customers.show', $customer) }}">
        <div class="flex flex-wrap justify-between gap-3">
            <div><b>{{ $customer->user->name }}</b><div class="text-sm text-slate-500">{{ $customer->customer_code }} | {{ $customer->user->email }} | {{ $customer->user->phone ?: '-' }}</div></div>
            <div>{{ $customer->status }}</div>
        </div>
    </a>
@endforeach
</div>
<div class="mt-5">{{ $customers->links() }}</div>
@endsection
