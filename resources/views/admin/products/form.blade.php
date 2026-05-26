@extends('layouts.admin')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update',$product) : route('admin.products.store') }}" class="grid gap-5 rounded bg-white p-6 shadow">@csrf
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
        <label class="grid gap-2"><span class="font-semibold">Gambar utama barang</span><input class="rounded border p-3" name="image" type="file" accept="image/*"></label>
        <label class="grid gap-2"><span class="font-semibold">Sample desain / referensi hasil</span><input class="rounded border p-3" name="sample_images[]" type="file" accept="image/*" multiple></label>
    </div>
    @if($product->image || $product->sample_images)
        <div class="rounded bg-slate-50 p-4 text-sm text-slate-600">Gambar tersimpan: {{ $product->image ?: '-' }} | Sample: {{ collect($product->sample_images)->count() }}</div>
    @endif
    <div class="rounded border p-4">
        <h2 class="mb-3 font-bold">Opsi Produk</h2>
        @for($i=0;$i<4;$i++)
            @php($option = $product->options[$i] ?? null)
            <div class="mb-3 grid gap-3 md:grid-cols-4">
                <input class="rounded border p-3" name="option_type[]" value="{{ $option->option_type ?? '' }}" placeholder="Tipe: bahan/finishing">
                <input class="rounded border p-3" name="option_name[]" value="{{ $option->name ?? '' }}" placeholder="Nama opsi">
                <input class="rounded border p-3" name="option_price[]" type="number" value="{{ $option->price_modifier ?? 0 }}" placeholder="Tambahan harga">
                <select class="rounded border p-3" name="option_calc[]"><option value="fixed">fixed</option><option value="per_qty">per_qty</option><option value="per_square_meter">per_square_meter</option></select>
            </div>
        @endfor
    </div>
    <div class="rounded border p-4">
        <h2 class="mb-3 font-bold">Kebutuhan Bahan Produksi</h2>
        @for($i=0;$i<4;$i++)
            @php($usage = $product->materialUsages[$i] ?? null)
            <div class="mb-3 grid gap-3 md:grid-cols-4">
                <select class="rounded border p-3" name="material_id[]"><option value="">Pilih bahan</option>@foreach($materials as $material)<option value="{{ $material->id }}" @selected($usage?->material_id===$material->id)>{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select>
                <input class="rounded border p-3" name="usage_per_unit[]" type="number" step="0.0001" value="{{ $usage->usage_per_unit ?? 1 }}" placeholder="Pemakaian">
                <select class="rounded border p-3" name="usage_type[]"><option value="per_item">per item</option><option value="per_meter">per meter</option><option value="per_square_meter">per m2</option><option value="per_package">per paket</option></select>
                <label class="flex items-center gap-2"><input type="radio" name="primary_material" value="{{ $i }}" @checked($usage?->is_primary)> Bahan utama</label>
            </div>
        @endfor
    </div>
    <button class="rounded bg-green-600 px-5 py-3 text-white">Simpan Produk</button>
</form>
@endsection
