@extends('layouts.app')
@section('content')
<div class="grid gap-8 lg:grid-cols-2">
    <section class="rounded bg-white p-6 shadow">
        @if($product->image && \Illuminate\Support\Facades\Storage::exists($product->image))
            <img class="mb-5 h-64 w-full rounded object-cover" src="{{ asset(str_replace('public/', 'storage/', $product->image)) }}" alt="{{ $product->name }}">
        @else
            <div class="mb-5 flex h-64 items-center justify-center rounded bg-green-100 text-8xl font-bold text-green-700">{{ substr($product->name, 0, 1) }}</div>
        @endif
        <p class="text-sm text-green-700">{{ $product->category->name }}</p>
        <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
        <p class="mt-3 text-slate-600">{{ $product->description }}</p>
        <p class="mt-3 text-sm text-slate-500">Estimasi pengerjaan {{ $product->estimated_days }} hari. Layanan: {{ $product->service_type }}. Metode: {{ $product->print_method ?: '-' }}.</p>
        @if($product->technical_specs)
            <h2 class="mt-5 font-bold">Spesifikasi Teknis</h2>
            <p class="text-sm text-slate-600">{{ $product->technical_specs }}</p>
        @endif
        @if($product->file_guidelines)
            <h2 class="mt-5 font-bold">Panduan File</h2>
            <p class="text-sm text-slate-600">{{ $product->file_guidelines }}</p>
        @endif
        @if($product->sample_images)
            <h2 class="mt-5 font-bold">Sample Desain</h2>
            <div class="mt-3 grid grid-cols-3 gap-3">
                @foreach($product->sample_images as $sample)
                    @if(\Illuminate\Support\Facades\Storage::exists($sample))
                        <img class="h-24 rounded object-cover" src="{{ asset(str_replace('public/', 'storage/', $sample)) }}" alt="Sample {{ $product->name }}">
                    @endif
                @endforeach
            </div>
        @endif
    </section>
    <section class="rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-2xl font-bold">Kalkulator Harga</h2>
        <form method="post" action="{{ route('cart.store', $product) }}" enctype="multipart/form-data" class="grid gap-4">@csrf
            @if($product->variants->count())
                <select class="rounded border p-3" name="product_variant_id">
                    <option value="">Pilih varian</option>
                    @foreach($product->variants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->name }} - Rp {{ number_format($variant->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            @endif
            @if($product->is_custom_size)
                <div class="grid grid-cols-2 gap-3">
                    <input class="rounded border p-3" name="width" type="number" step="0.01" min="0.01" placeholder="Lebar">
                    <input class="rounded border p-3" name="height" type="number" step="0.01" min="0.01" placeholder="Tinggi">
                </div>
            @endif
            <input class="rounded border p-3" name="quantity" type="number" min="{{ $product->min_order_qty }}" value="{{ $product->min_order_qty }}">
            @foreach($product->options->groupBy('option_type') as $type => $options)
                <label class="grid gap-2">
                    <span class="font-semibold capitalize">{{ $type }}</span>
                    @foreach($options as $option)
                        <span><input type="checkbox" name="options[]" value="{{ $option->id }}"> {{ $option->name }} (+Rp {{ number_format($option->price_modifier, 0, ',', '.') }})</span>
                    @endforeach
                </label>
            @endforeach
            <textarea class="rounded border p-3" name="customer_note" placeholder="Catatan pesanan"></textarea>
            <input class="rounded border p-3" name="design_file" type="file" accept=".pdf,.png,.jpg,.jpeg,.ai,.cdr,.psd">
            <div class="rounded bg-green-50 p-4 text-green-900">Harga akan dihitung saat item dimasukkan ke keranjang sesuai spesifikasi yang dipilih.</div>
            @auth
                @if(auth()->user()->hasRole('customer'))
                    <button class="rounded bg-green-600 px-5 py-3 font-semibold text-white">Tambah ke Keranjang</button>
                @else
                    <a class="rounded bg-slate-700 px-5 py-3 text-center font-semibold text-white" href="{{ route('admin.products.edit', $product) }}">Edit Produk</a>
                @endif
            @else
                <a class="rounded bg-green-600 px-5 py-3 text-center font-semibold text-white" href="{{ route('login') }}">Login untuk Pesan</a>
            @endauth
        </form>
    </section>
</div>
@endsection
