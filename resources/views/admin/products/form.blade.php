@extends('layouts.admin')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update',$product) : route('admin.products.store') }}" class="grid gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">@csrf
    @if($product->exists) @method('put') @endif
    <div class="grid gap-4 md:grid-cols-2">
        <input class="rounded border p-3" name="product_code" value="{{ old('product_code',$product->product_code) }}" placeholder="Kode barang / SKU">
        <input class="rounded border p-3" name="name" value="{{ old('name',$product->name) }}" placeholder="Nama produk" required>
        <select class="rounded border p-3" name="category_id" required>
            @foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$product->category_id)==$category->id)>{{ $category->name }}</option>@endforeach
        </select>
        <input class="rounded border p-3" name="base_price" type="number" value="{{ old('base_price',$product->base_price ?? 0) }}" placeholder="Harga dasar" required>
        <select class="rounded border p-3" name="service_type">
            @foreach(['printing'=>'Printing','sablon'=>'Sablon','design'=>'Desain','finishing'=>'Finishing','merchandise'=>'Merchandise'] as $key => $label)<option value="{{ $key }}" @selected(old('service_type',$product->service_type ?? 'printing')===$key)>{{ $label }}</option>@endforeach
        </select>
        <select class="rounded border p-3" name="pricing_type">
            @foreach(['pcs','meter','square_meter','package','rim','manual'] as $type)<option value="{{ $type }}" @selected(old('pricing_type',$product->pricing_type)===$type)>{{ $type }}</option>@endforeach
        </select>
        <input class="rounded border p-3" name="print_method" value="{{ old('print_method',$product->print_method) }}" placeholder="Metode: DTG, DTF, screen printing, offset">
        <input class="rounded border p-3" name="default_material" value="{{ old('default_material',$product->default_material) }}" placeholder="Bahan default">
        <input class="rounded border p-3" name="waste_percentage" type="number" step="0.01" value="{{ old('waste_percentage',$product->waste_percentage ?? 0) }}" placeholder="Waste % produksi">
        <input class="rounded border p-3" name="unit" value="{{ old('unit',$product->unit ?? 'pcs') }}" placeholder="Satuan">
        <input class="rounded border p-3" name="estimated_days" type="number" value="{{ old('estimated_days',$product->estimated_days ?? 1) }}" placeholder="Estimasi hari">
        <input class="rounded border p-3" name="min_order_qty" type="number" value="{{ old('min_order_qty',$product->min_order_qty ?? 1) }}" placeholder="Minimal order">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_custom_size" value="1" @checked(old('is_custom_size',$product->is_custom_size))> Custom ukuran</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$product->is_active ?? true))> Aktif</label>
    </div>
    <textarea class="rounded border p-3" name="description" placeholder="Deskripsi">{{ old('description',$product->description) }}</textarea>
    <textarea class="rounded border p-3" name="technical_specs" placeholder="Spesifikasi teknis produk, contoh ukuran cetak, resolusi, bahan sablon, warna tinta">{{ old('technical_specs',$product->technical_specs) }}</textarea>
    <textarea class="rounded border p-3" name="file_guidelines" placeholder="Panduan file desain, contoh format PDF/AI/CDR, bleed, resolusi minimum">{{ old('file_guidelines',$product->file_guidelines) }}</textarea>
    <div class="grid gap-4 md:grid-cols-2">
        <label class="grid gap-2"><span class="font-semibold">Gambar utama barang</span><input id="productImageInput" class="rounded border p-3" name="image" type="file" accept="image/*"></label>
        <label class="grid gap-2"><span class="font-semibold">Sample desain / referensi hasil</span><input id="sampleImagesInput" class="rounded border p-3" name="sample_images[]" type="file" accept="image/*" multiple></label>
    </div>
    @if($product->image || $product->sample_images)
        <div class="grid gap-3 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
            @if($product->image)
                <div>
                    <div class="mb-2 font-semibold text-slate-800">Gambar utama tersimpan</div>
                    <img class="h-40 w-64 rounded-xl border border-slate-200 object-cover" src="{{ $mediaUrl($product->image) }}" alt="{{ $product->name }}" loading="lazy" decoding="async" sizes="256px">
                </div>
            @endif
            @if($product->sample_images)
                <div>
                    <div class="mb-2 font-semibold text-slate-800">Sample desain tersimpan</div>
                    <div class="grid gap-3 sm:grid-cols-4">
                        @foreach($product->sample_images as $sample)
                            <img class="h-28 w-full rounded-xl border border-slate-200 object-cover" src="{{ $mediaUrl($sample) }}" alt="Sample {{ $product->name }}" loading="lazy" decoding="async">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
    <div id="newImagePreviewWrap" class="hidden rounded-xl border border-emerald-100 bg-emerald-50 p-4">
        <div class="mb-2 font-semibold text-emerald-900">Preview gambar baru</div>
        <img id="productImagePreview" class="h-40 w-64 rounded-xl border border-emerald-200 bg-white object-cover" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Preview gambar produk" decoding="async">
    </div>
    <div id="newSamplesPreview" class="hidden grid gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 sm:grid-cols-4"></div>
    <div class="rounded border p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Varian Harga / Ukuran</h2>
                <p class="text-sm text-slate-500">Contoh sablon/jersey: S, M, L, XL, XXL dengan harga berbeda.</p>
            </div>
            <button type="button" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" onclick="addVariantRow()">+ Tambah Varian</button>
        </div>
        <div id="variantRows" class="grid gap-3">
            @php($variants = $product->variants->count() ? $product->variants : collect([null]))
            @foreach($variants as $variant)
                <div class="variant-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-6">
                    <input class="rounded border p-3 md:col-span-2" name="variant_name[]" value="{{ $variant->name ?? '' }}" placeholder="Nama varian: XL / L / 100 pcs">
                    <input class="rounded border p-3" name="variant_sku[]" value="{{ $variant->sku ?? '' }}" placeholder="SKU">
                    <input class="rounded border p-3" name="variant_price[]" type="number" value="{{ $variant->price ?? 0 }}" placeholder="Harga">
                    <input class="rounded border p-3" name="variant_min_qty[]" type="number" value="{{ $variant->min_qty ?? '' }}" placeholder="Min qty">
                    <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.variant-row')">Hapus</button>
                    <input type="hidden" name="variant_max_qty[]" value="{{ $variant->max_qty ?? '' }}">
                </div>
            @endforeach
        </div>
    </div>
    <div class="rounded border p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Opsi Produk</h2>
                <p class="text-sm text-slate-500">Untuk pilihan bahan, metode sablon, finishing, laminasi, warna, dan biaya tambahan.</p>
            </div>
            <button type="button" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" onclick="addOptionRow()">+ Tambah Opsi</button>
        </div>
        <div id="optionRows" class="grid gap-3">
            @php($options = $product->options->count() ? $product->options : collect([null]))
            @foreach($options as $option)
                <div class="option-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-5">
                    <input class="rounded border p-3" name="option_type[]" value="{{ $option->option_type ?? '' }}" placeholder="Tipe: bahan/finishing/size">
                    <input class="rounded border p-3" name="option_name[]" value="{{ $option->name ?? '' }}" placeholder="Nama opsi">
                    <input class="rounded border p-3" name="option_price[]" type="number" value="{{ $option->price_modifier ?? 0 }}" placeholder="Tambahan harga">
                    <select class="rounded border p-3" name="option_calc[]">
                        @foreach(['fixed','per_qty','per_meter','per_square_meter'] as $calc)
                            <option value="{{ $calc }}" @selected(($option->calculation_type ?? 'fixed') === $calc)>{{ $calc }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.option-row')">Hapus</button>
                </div>
            @endforeach
        </div>
    </div>
    <div class="rounded border p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Kebutuhan Bahan Produksi / BOM</h2>
                <p class="text-sm text-slate-500">Contoh banner 2x1m mengurangi Flexi China 2 m2. Kaos sablon bisa mengurangi blank kaos dan tinta/film per item.</p>
            </div>
            <button type="button" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" onclick="addBomRow()">+ Tambah Bahan</button>
        </div>
        <div id="bomRows" class="grid gap-3">
            @php($usages = $product->materialUsages->count() ? $product->materialUsages : collect([null]))
            @foreach($usages as $i => $usage)
                <div class="bom-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-5">
                    <select class="rounded border p-3" name="material_id[]"><option value="">Pilih bahan</option>@foreach($materials as $material)<option value="{{ $material->id }}" @selected($usage?->material_id===$material->id)>{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select>
                    <input class="rounded border p-3" name="usage_per_unit[]" type="number" step="0.0001" value="{{ $usage->usage_per_unit ?? 1 }}" placeholder="Pemakaian">
                    <select class="rounded border p-3" name="usage_type[]">
                        @foreach(['per_item' => 'per item', 'per_meter' => 'per meter', 'per_square_meter' => 'per m2', 'per_package' => 'per paket'] as $key => $label)
                            <option value="{{ $key }}" @selected(($usage->usage_type ?? 'per_item') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-2"><input class="bom-primary" type="radio" name="primary_material" value="{{ $i }}" @checked($usage?->is_primary)> Bahan utama</label>
                    <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.bom-row'); refreshBomPrimaryIndexes();">Hapus</button>
                </div>
            @endforeach
        </div>
    </div>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Simpan Produk</button>
</form>

<template id="variantTemplate">
    <div class="variant-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-6">
        <input class="rounded border p-3 md:col-span-2" name="variant_name[]" placeholder="Nama varian: XL / L / 100 pcs">
        <input class="rounded border p-3" name="variant_sku[]" placeholder="SKU">
        <input class="rounded border p-3" name="variant_price[]" type="number" value="0" placeholder="Harga">
        <input class="rounded border p-3" name="variant_min_qty[]" type="number" placeholder="Min qty">
        <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.variant-row')">Hapus</button>
        <input type="hidden" name="variant_max_qty[]">
    </div>
</template>
<template id="optionTemplate">
    <div class="option-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-5">
        <input class="rounded border p-3" name="option_type[]" placeholder="Tipe: bahan/finishing/size">
        <input class="rounded border p-3" name="option_name[]" placeholder="Nama opsi">
        <input class="rounded border p-3" name="option_price[]" type="number" value="0" placeholder="Tambahan harga">
        <select class="rounded border p-3" name="option_calc[]"><option value="fixed">fixed</option><option value="per_qty">per_qty</option><option value="per_meter">per_meter</option><option value="per_square_meter">per_square_meter</option></select>
        <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.option-row')">Hapus</button>
    </div>
</template>
<template id="bomTemplate">
    <div class="bom-row grid gap-3 rounded border border-slate-200 p-3 md:grid-cols-5">
        <select class="rounded border p-3" name="material_id[]"><option value="">Pilih bahan</option>@foreach($materials as $material)<option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select>
        <input class="rounded border p-3" name="usage_per_unit[]" type="number" step="0.0001" value="1" placeholder="Pemakaian">
        <select class="rounded border p-3" name="usage_type[]"><option value="per_item">per item</option><option value="per_meter">per meter</option><option value="per_square_meter">per m2</option><option value="per_package">per paket</option></select>
        <label class="flex items-center gap-2"><input class="bom-primary" type="radio" name="primary_material" value="0"> Bahan utama</label>
        <button type="button" class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-600" onclick="removeDynamicRow(this, '.bom-row'); refreshBomPrimaryIndexes();">Hapus</button>
    </div>
</template>
<script>
    function appendTemplate(templateId, targetId) {
        const template = document.getElementById(templateId);
        document.getElementById(targetId).appendChild(template.content.cloneNode(true));
    }
    function addVariantRow() {
        appendTemplate('variantTemplate', 'variantRows');
    }
    function addOptionRow() {
        appendTemplate('optionTemplate', 'optionRows');
    }
    function addBomRow() {
        appendTemplate('bomTemplate', 'bomRows');
        refreshBomPrimaryIndexes();
    }
    function removeDynamicRow(button, selector) {
        const row = button.closest(selector);
        const container = row?.parentElement;
        if (!row || !container || container.querySelectorAll(selector).length <= 1) {
            row?.querySelectorAll('input, select').forEach((field) => {
                if (field.type === 'radio') field.checked = false;
                else field.value = field.type === 'number' ? 0 : '';
            });
            return;
        }
        row.remove();
    }
    function refreshBomPrimaryIndexes() {
        document.querySelectorAll('#bomRows .bom-primary').forEach((radio, index) => {
            radio.value = index;
        });
    }
    refreshBomPrimaryIndexes();
    document.getElementById('productImageInput')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        const wrap = document.getElementById('newImagePreviewWrap');
        const image = document.getElementById('productImagePreview');
        if (!file || !image || !wrap) return;
        image.src = URL.createObjectURL(file);
        wrap.classList.remove('hidden');
    });
    document.getElementById('sampleImagesInput')?.addEventListener('change', (event) => {
        const wrap = document.getElementById('newSamplesPreview');
        if (!wrap) return;
        wrap.innerHTML = '';
        [...(event.target.files || [])].forEach((file) => {
            if (!file.type.startsWith('image/')) return;
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'h-28 w-full rounded-xl border border-emerald-200 bg-white object-cover';
            img.alt = 'Preview sample';
            wrap.appendChild(img);
        });
        wrap.classList.toggle('hidden', !wrap.children.length);
    });
</script>
@endsection
