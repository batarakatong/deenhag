@extends('layouts.admin')
@section('content')
<h1 class="mb-5 text-2xl font-bold">Detail Customer</h1>
<div class="grid gap-6 lg:grid-cols-3">
    <form method="post" action="{{ route('admin.customers.update', $customer) }}" enctype="multipart/form-data" class="grid gap-4 rounded-xl bg-white p-6 shadow lg:col-span-2">@csrf @method('patch')
        <div class="flex items-center gap-4">
            @if($customer->user->avatar_path)
                <img class="h-20 w-20 rounded-full object-cover" src="{{ $mediaUrl($customer->user->avatar_path) }}" alt="{{ $customer->user->name }}" decoding="async">
            @else
                <div class="grid h-20 w-20 place-items-center rounded-full bg-green-100 text-2xl font-bold text-green-700">{{ substr($customer->user->name,0,1) }}</div>
            @endif
            <label class="grid gap-2">
                <span class="text-sm font-semibold">Foto Profil</span>
                <input class="field" name="avatar" type="file" accept="image/*">
            </label>
        </div>
        <input class="field" name="name" value="{{ $customer->user->name }}" required>
        <input class="field" name="email" type="email" value="{{ $customer->user->email }}" required>
        <input class="field" name="phone" value="{{ $customer->user->phone }}" placeholder="No HP / WA">
        <input class="field" name="company_name" value="{{ $customer->company_name }}" placeholder="Perusahaan">
        <textarea class="field" name="address" placeholder="Alamat">{{ $customer->address }}</textarea>
        <div class="grid gap-3 md:grid-cols-3">
            <input class="field" name="city" value="{{ $customer->city }}" placeholder="Kota">
            <input class="field" name="province" value="{{ $customer->province }}" placeholder="Provinsi">
            <input class="field" name="postal_code" value="{{ $customer->postal_code }}" placeholder="Kode pos">
        </div>
        <select class="field" name="status"><option value="active" @selected($customer->status==='active')>Aktif</option><option value="inactive" @selected($customer->status==='inactive')>Nonaktif</option></select>
        <label class="flex items-center gap-2"><input type="checkbox" name="two_factor_enabled" value="1" @checked($customer->user->two_factor_enabled)> Aktifkan 2FA akun ini</label>
        <button class="btn btn-primary">Update Customer</button>
    </form>
    <aside class="grid gap-6">
        <form method="post" action="{{ route('admin.customers.reset-password', $customer) }}" class="grid gap-3 rounded-xl bg-white p-6 shadow">@csrf
            <h2 class="font-bold">Reset Password</h2>
            <input class="field" name="password" type="password" placeholder="Password baru" required>
            <input class="field" name="password_confirmation" type="password" placeholder="Konfirmasi password" required>
            <button class="btn btn-danger">Reset Password</button>
        </form>
        <div class="rounded-xl bg-white p-6 shadow">
            <h2 class="mb-3 font-bold">Riwayat Pesanan</h2>
            @foreach($customer->orders as $order)
                <a class="block border-b py-2 text-sm" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }} - Rp {{ number_format($order->grand_total,0,',','.') }}</a>
            @endforeach
        </div>
    </aside>
</div>
@endsection
