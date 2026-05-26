@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Checkout</h1>
<form method="post" action="{{ route('checkout.store') }}" class="grid gap-6 lg:grid-cols-3">@csrf
    <div class="grid gap-4 rounded bg-white p-6 shadow lg:col-span-2">
        <input class="rounded border p-3" name="recipient_name" value="{{ auth()->user()->name }}" placeholder="Nama penerima" required>
        <input class="rounded border p-3" name="phone" value="{{ auth()->user()->phone }}" placeholder="Nomor HP">
        <textarea class="rounded border p-3" name="address" placeholder="Alamat pengiriman"></textarea>
        <select class="rounded border p-3" name="fulfillment_method">
            <option value="pickup">Ambil di toko</option>
            <option value="delivery">Dikirim kurir</option>
        </select>
        <input class="rounded border p-3" name="shipping_cost" type="number" min="0" value="0" placeholder="Ongkir jika dikirim">
        <textarea class="rounded border p-3" name="customer_note" placeholder="Catatan order"></textarea>
    </div>
    <aside class="rounded bg-white p-6 shadow">
        <h2 class="mb-3 font-bold">Ringkasan</h2>
        @foreach($cart->items as $item)
            <div class="mb-3 border-b pb-3 text-sm">{{ $item->product->name }} <span class="float-right">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</span></div>
        @endforeach
        <div class="text-xl font-bold">Rp {{ number_format($cart->items->sum('estimated_price'), 0, ',', '.') }}</div>
        <button class="mt-5 w-full rounded bg-green-600 px-5 py-3 font-semibold text-white">Buat Pesanan</button>
    </aside>
</form>
@endsection
