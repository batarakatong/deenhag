@extends('layouts.app')
@section('content')
<div class="grid gap-8 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        @if($product->image)
            <img class="mb-5 h-72 w-full rounded-2xl bg-emerald-50 object-cover" src="{{ $mediaUrl($product->image) }}" alt="{{ $product->name }}" fetchpriority="high" decoding="async" sizes="(min-width: 1024px) 50vw, 100vw">
        @else
            <div class="mb-5 flex h-72 items-center justify-center rounded-2xl bg-green-100 text-8xl font-bold text-green-700">{{ substr($product->name, 0, 1) }}</div>
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
            <div class="mt-6">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="font-bold">Sample Desain / Referensi Hasil</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ collect($product->sample_images)->count() }} gambar</span>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                    <div class="relative">
                        @foreach($product->sample_images as $index => $sample)
                            <button type="button" class="sample-slide {{ $index === 0 ? '' : 'hidden' }} block w-full" data-index="{{ $index }}" data-src="{{ $mediaUrl($sample) }}" aria-label="Zoom sample {{ $index + 1 }}">
                                <img class="h-80 w-full bg-white object-contain" src="{{ $mediaUrl($sample) }}" alt="Sample {{ $product->name }} {{ $index + 1 }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async" sizes="(min-width: 1024px) 50vw, 100vw">
                            </button>
                        @endforeach
                        @if(collect($product->sample_images)->count() > 1)
                            <button type="button" class="sample-prev absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 font-bold text-emerald-700 shadow-lg" aria-label="Sample sebelumnya">&lt;</button>
                            <button type="button" class="sample-next absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 font-bold text-emerald-700 shadow-lg" aria-label="Sample berikutnya">&gt;</button>
                        @endif
                    </div>
                    <div class="flex gap-2 overflow-x-auto border-t border-slate-100 bg-white p-3">
                        @foreach($product->sample_images as $index => $sample)
                            <button type="button" class="sample-thumb h-16 w-20 shrink-0 rounded-xl border-2 {{ $index === 0 ? 'border-emerald-500' : 'border-transparent' }} bg-slate-50 p-1" data-index="{{ $index }}" aria-label="Buka sample {{ $index + 1 }}">
                                <img class="h-full w-full rounded-lg object-cover" src="{{ $mediaUrl($sample) }}" alt="Thumbnail sample {{ $index + 1 }}" loading="lazy" decoding="async" sizes="80px">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>
    <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-2xl font-bold">Kalkulator Harga</h2>
        <form method="post" action="{{ route('cart.store', $product) }}" enctype="multipart/form-data" class="grid gap-4">@csrf
            @if($product->variants->count())
                <div class="grid gap-3 rounded border border-emerald-100 bg-emerald-50/60 p-4">
                    <label class="grid gap-2">
                        <span class="font-semibold">Varian utama</span>
                        <select class="rounded border p-3" name="product_variant_id">
                            <option value="">Pilih varian jika hanya satu ukuran/paket</option>
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->name }} - Rp {{ number_format($variant->price ?: $product->base_price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div>
                        <div class="mb-2 flex flex-wrap items-end justify-between gap-2">
                            <div>
                                <p class="font-semibold">Multi varian dan kuantiti</p>
                                <p class="text-sm text-slate-600">Contoh pesanan kaos sablon/jersey: XL 10 pcs, L 5 pcs dalam satu order.</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-emerald-700">Total: <span id="variantQtyTotal">0</span> pcs</span>
                        </div>
                        <div class="overflow-x-auto rounded border bg-white">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 text-left text-slate-600">
                                    <tr>
                                        <th class="p-3">Varian</th>
                                        <th class="p-3">Harga</th>
                                        <th class="p-3">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr class="border-t">
                                            <td class="p-3 font-semibold">{{ $variant->name }}</td>
                                            <td class="p-3">Rp {{ number_format($variant->price ?: $product->base_price, 0, ',', '.') }}</td>
                                            <td class="p-3">
                                                <input class="variant-qty w-28 rounded border p-2" name="variant_quantities[{{ $variant->id }}]" type="number" min="0" value="0" inputmode="numeric">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
            <input id="designFileInput" class="rounded border p-3" name="design_file" type="file" accept=".pdf,.png,.jpg,.jpeg,.ai,.cdr,.psd">
            <img id="designFilePreview" class="hidden max-h-52 w-full rounded-xl border border-slate-200 object-contain p-2" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Preview desain" decoding="async">
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
@if($product->sample_images)
    <div id="sampleLightbox" class="fixed inset-0 z-50 hidden bg-slate-950/90 p-4">
        <button type="button" id="sampleLightboxClose" class="absolute right-5 top-5 rounded-full bg-white px-4 py-2 font-bold text-slate-900">Tutup</button>
        @if(collect($product->sample_images)->count() > 1)
            <button type="button" id="sampleLightboxPrev" class="absolute left-5 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-xl font-bold text-emerald-700">&lt;</button>
            <button type="button" id="sampleLightboxNext" class="absolute right-5 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-xl font-bold text-emerald-700">&gt;</button>
        @endif
        <div class="flex h-full items-center justify-center">
            <img id="sampleLightboxImage" class="max-h-[86vh] max-w-[92vw] rounded-2xl bg-white object-contain shadow-2xl" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Zoom sample desain" decoding="async">
        </div>
    </div>
@endif
<script>
    const sampleSlides = [...document.querySelectorAll('.sample-slide')];
    const sampleThumbs = [...document.querySelectorAll('.sample-thumb')];
    const sampleLightbox = document.getElementById('sampleLightbox');
    const sampleLightboxImage = document.getElementById('sampleLightboxImage');
    let sampleIndex = 0;

    function showSample(index) {
        if (!sampleSlides.length) return;
        sampleIndex = (index + sampleSlides.length) % sampleSlides.length;
        sampleSlides.forEach((slide, slideIndex) => slide.classList.toggle('hidden', slideIndex !== sampleIndex));
        sampleThumbs.forEach((thumb, thumbIndex) => {
            thumb.classList.toggle('border-emerald-500', thumbIndex === sampleIndex);
            thumb.classList.toggle('border-transparent', thumbIndex !== sampleIndex);
        });
        if (sampleLightbox && !sampleLightbox.classList.contains('hidden')) {
            sampleLightboxImage.src = sampleSlides[sampleIndex].dataset.src;
        }
    }

    function openSampleLightbox(index) {
        showSample(index);
        if (!sampleLightbox || !sampleLightboxImage) return;
        sampleLightboxImage.src = sampleSlides[sampleIndex].dataset.src;
        sampleLightbox.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSampleLightbox() {
        sampleLightbox?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelector('.sample-prev')?.addEventListener('click', () => showSample(sampleIndex - 1));
    document.querySelector('.sample-next')?.addEventListener('click', () => showSample(sampleIndex + 1));
    document.getElementById('sampleLightboxPrev')?.addEventListener('click', () => showSample(sampleIndex - 1));
    document.getElementById('sampleLightboxNext')?.addEventListener('click', () => showSample(sampleIndex + 1));
    document.getElementById('sampleLightboxClose')?.addEventListener('click', closeSampleLightbox);
    sampleLightbox?.addEventListener('click', (event) => {
        if (event.target === sampleLightbox) closeSampleLightbox();
    });
    sampleSlides.forEach((slide, index) => slide.addEventListener('click', () => openSampleLightbox(index)));
    sampleThumbs.forEach((thumb, index) => thumb.addEventListener('click', () => showSample(index)));
    document.addEventListener('keydown', (event) => {
        if (!sampleLightbox || sampleLightbox.classList.contains('hidden')) return;
        if (event.key === 'Escape') closeSampleLightbox();
        if (event.key === 'ArrowLeft') showSample(sampleIndex - 1);
        if (event.key === 'ArrowRight') showSample(sampleIndex + 1);
    });

    function refreshVariantQtyTotal() {
        const total = [...document.querySelectorAll('.variant-qty')]
            .reduce((sum, input) => sum + Math.max(parseInt(input.value || '0', 10), 0), 0);
        const target = document.getElementById('variantQtyTotal');
        if (target) target.textContent = total;
    }
    document.querySelectorAll('.variant-qty').forEach((input) => {
        input.addEventListener('input', refreshVariantQtyTotal);
    });
    refreshVariantQtyTotal();
    document.getElementById('designFileInput')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        const preview = document.getElementById('designFilePreview');
        if (!file || !preview || !file.type.startsWith('image/')) {
            preview?.classList.add('hidden');
            return;
        }
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });
</script>
@endsection
