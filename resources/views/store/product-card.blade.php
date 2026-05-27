<a href="{{ route('products.show', $product) }}" class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    @if($product->image)
        <img class="mb-4 h-36 w-full rounded-xl bg-emerald-50 object-cover" src="{{ $mediaUrl($product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw">
    @else
        <div class="mb-4 flex h-36 items-center justify-center rounded-xl bg-green-100 text-5xl font-bold text-green-700">{{ substr($product->name, 0, 1) }}</div>
    @endif
    <div class="text-sm text-green-700">{{ $product->category->name }}</div>
    <div class="text-lg font-bold">{{ $product->name }}</div>
    <div class="mt-2 text-sm text-slate-600">{{ ucfirst($product->service_type ?? 'printing') }} - Mulai Rp {{ number_format($product->base_price, 0, ',', '.') }} / {{ $product->unit }}</div>
    <div class="mt-1 text-sm text-slate-500">Estimasi {{ $product->estimated_days }} hari</div>
</a>
