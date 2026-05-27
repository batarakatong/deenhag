@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Keranjang</h1>
@if($cart->items->isEmpty())
    <div class="rounded bg-white p-6 shadow">Keranjang kosong. <a class="text-green-700" href="{{ route('products.index') }}">Pilih produk</a>.</div>
@else
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="grid gap-4 lg:col-span-2">
            @foreach($cart->items as $item)
                <div class="rounded bg-white p-5 shadow">
                    <div class="flex justify-between gap-4">
                        <div>
                            <div class="font-bold">{{ $item->product->name }}</div>
                            <div class="text-sm text-slate-600">Qty {{ $item->quantity }} | Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</div>
                            <div class="mt-2 text-sm text-slate-500">@include('partials.specifications', ['specifications' => $item->specifications])</div>
                            @if($item->design_file_path)
                                @php($isImage = str($item->design_file_name)->lower()->endsWith(['.jpg', '.jpeg', '.png', '.webp']))
                                <div class="mt-3">
                                    @if($isImage)
                                        <img class="h-28 w-40 rounded-lg border object-cover" src="{{ route('files.cart-design', $item) }}" alt="{{ $item->design_file_name }}" loading="lazy" decoding="async" sizes="160px">
                                    @endif
                                    <a class="mt-2 inline-block text-sm font-semibold text-green-700" href="{{ route('files.cart-design', $item) }}" target="_blank">{{ $item->design_file_name }}</a>
                                </div>
                            @endif
                        </div>
                        <form method="post" action="{{ route('cart.items.destroy', $item) }}">@csrf @method('delete')<button class="text-red-600">Hapus</button></form>
                    </div>
                </div>
            @endforeach
        </div>
        <aside class="rounded bg-white p-5 shadow">
            <div class="text-sm text-slate-500">Subtotal</div>
            <div class="text-2xl font-bold">Rp {{ number_format($cart->items->sum('estimated_price'), 0, ',', '.') }}</div>
            <a class="mt-5 block rounded bg-green-600 px-5 py-3 text-center font-semibold text-white" href="{{ route('checkout.index') }}">Checkout</a>
        </aside>
    </div>
@endif
@endsection
