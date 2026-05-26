<a href="{{ route('products.show', $product) }}" class="rounded bg-white p-5 shadow transition hover:-translate-y-1">
    @if($product->image && \Illuminate\Support\Facades\Storage::exists($product->image))
        <img class="mb-4 h-32 w-full rounded object-cover" src="{{ asset(str_replace('public/', 'storage/', $product->image)) }}" alt="{{ $product->name }}">
    @else
        <div class="mb-4 flex h-32 items-center justify-center rounded bg-green-100 text-5xl font-bold text-green-700">{{ substr($product->name, 0, 1) }}</div>
    @endif
    <div class="text-sm text-green-700">{{ $product->category->name }}</div>
    <div class="text-lg font-bold">{{ $product->name }}</div>
    <div class="mt-2 text-sm text-slate-600">{{ ucfirst($product->service_type ?? 'printing') }} - Mulai Rp {{ number_format($product->base_price, 0, ',', '.') }} / {{ $product->unit }}</div>
    <div class="mt-1 text-sm text-slate-500">Estimasi {{ $product->estimated_days }} hari</div>
</a>
